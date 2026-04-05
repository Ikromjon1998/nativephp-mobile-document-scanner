package com.nativephp.documentscanner

import android.app.Activity
import android.content.Intent
import android.util.Log
import androidx.activity.result.ActivityResult
import androidx.activity.result.ActivityResultLauncher
import androidx.activity.result.IntentSenderRequest
import androidx.activity.result.contract.ActivityResultContracts
import androidx.fragment.app.FragmentActivity
import com.google.mlkit.vision.documentscanner.GmsDocumentScanning
import com.google.mlkit.vision.documentscanner.GmsDocumentScannerOptions
import com.google.mlkit.vision.documentscanner.GmsDocumentScanningResult
import com.nativephp.mobile.bridge.BridgeFunction
import com.nativephp.mobile.utils.NativeActionCoordinator
import org.json.JSONArray
import org.json.JSONObject
import java.io.File
import java.io.FileOutputStream

object DocumentScannerFunctions {
    private const val TAG = "DocumentScanner"

    private var defaultMaxPages = 0
    private var defaultOutputFormat = "jpeg"
    private var defaultJpegQuality = 90
    private var maxPagesLimit = 100
    private var storageDirectory = "scanned-documents"
    private var defaultGalleryImport = false
    private var defaultScannerMode = "full"

    private var scannerLauncher: ActivityResultLauncher<IntentSenderRequest>? = null
    private var currentOutputFormat = "jpeg"

    fun applyConfig(config: Map<*, *>) {
        (config["default_max_pages"] as? Number)?.let { defaultMaxPages = it.toInt() }
        (config["default_output_format"] as? String)?.let { defaultOutputFormat = it }
        (config["default_jpeg_quality"] as? Number)?.let { defaultJpegQuality = it.toInt() }
        (config["max_pages_limit"] as? Number)?.let { maxPagesLimit = it.toInt() }
        (config["storage_directory"] as? String)?.let { storageDirectory = it }
        (config["default_gallery_import"] as? Boolean)?.let { defaultGalleryImport = it }
        (config["default_scanner_mode"] as? String)?.let { defaultScannerMode = it }
    }

    private fun getStorageDir(activity: Activity): File {
        val dir = File(activity.filesDir, storageDirectory)
        if (!dir.exists()) {
            dir.mkdirs()
        }
        return dir
    }

    private fun dispatchEvent(activity: FragmentActivity, event: String, payload: JSONObject) {
        activity.runOnUiThread {
            NativeActionCoordinator.dispatchEvent(activity, event, payload.toString())
        }
    }

    fun registerLauncher(activity: FragmentActivity) {
        if (scannerLauncher != null) return

        scannerLauncher = activity.registerForActivityResult(
            ActivityResultContracts.StartIntentSenderForResult()
        ) { result: ActivityResult ->
            if (result.resultCode == Activity.RESULT_OK) {
                handleScanResult(activity, result.data)
            } else {
                val payload = JSONObject()
                dispatchEvent(
                    activity,
                    "Ikromjon\\DocumentScanner\\Events\\ScanCancelled",
                    payload
                )
            }
        }
    }

    private fun handleScanResult(activity: FragmentActivity, data: Intent?) {
        val scanResult = GmsDocumentScanningResult.fromActivityResultIntent(data)

        if (scanResult == null) {
            val payload = JSONObject().put("error", "Failed to retrieve scan result")
            dispatchEvent(
                activity,
                "Ikromjon\\DocumentScanner\\Events\\ScanFailed",
                payload
            )
            return
        }

        val dir = getStorageDir(activity)
        val timestamp = System.currentTimeMillis()
        val paths = JSONArray()

        if (currentOutputFormat == "pdf") {
            scanResult.pdf?.let { pdf ->
                val destFile = File(dir, "scan_${timestamp}.pdf")
                pdf.uri.let { uri ->
                    activity.contentResolver.openInputStream(uri)?.use { input ->
                        FileOutputStream(destFile).use { output ->
                            input.copyTo(output)
                        }
                    }
                }
                paths.put(destFile.absolutePath)
            }
        } else {
            scanResult.pages?.forEachIndexed { index, page ->
                val destFile = File(dir, "scan_${timestamp}_${index}.jpg")
                page.imageUri.let { uri ->
                    activity.contentResolver.openInputStream(uri)?.use { input ->
                        FileOutputStream(destFile).use { output ->
                            input.copyTo(output)
                        }
                    }
                }
                paths.put(destFile.absolutePath)
            }
        }

        val pageCount = if (currentOutputFormat == "pdf") {
            scanResult.pages?.size ?: 1
        } else {
            scanResult.pages?.size ?: 0
        }

        val payload = JSONObject()
            .put("paths", paths)
            .put("pageCount", pageCount)
            .put("outputFormat", currentOutputFormat)

        dispatchEvent(
            activity,
            "Ikromjon\\DocumentScanner\\Events\\DocumentScanned",
            payload
        )
    }

    class Scan(private val activity: FragmentActivity) : BridgeFunction {
        init {
            registerLauncher(activity)
        }

        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            @Suppress("UNCHECKED_CAST")
            (parameters["_config"] as? Map<String, Any>)?.let { applyConfig(it) }

            val maxPages = (parameters["maxPages"] as? Number)?.toInt() ?: defaultMaxPages
            val outputFormat = parameters["outputFormat"] as? String ?: defaultOutputFormat
            val jpegQuality = (parameters["jpegQuality"] as? Number)?.toInt() ?: defaultJpegQuality
            val galleryImport = parameters["galleryImport"] as? Boolean ?: defaultGalleryImport
            val scannerMode = parameters["scannerMode"] as? String ?: defaultScannerMode

            currentOutputFormat = outputFormat

            val mode = when (scannerMode) {
                "base" -> GmsDocumentScannerOptions.SCANNER_MODE_BASE
                "filter" -> GmsDocumentScannerOptions.SCANNER_MODE_BASE_WITH_FILTER
                else -> GmsDocumentScannerOptions.SCANNER_MODE_FULL
            }

            val optionsBuilder = GmsDocumentScannerOptions.Builder()
                .setGalleryImportAllowed(galleryImport)
                .setScannerMode(mode)

            if (outputFormat == "pdf") {
                optionsBuilder.setResultFormats(
                    GmsDocumentScannerOptions.RESULT_FORMAT_PDF,
                    GmsDocumentScannerOptions.RESULT_FORMAT_JPEG
                )
            } else {
                optionsBuilder.setResultFormats(
                    GmsDocumentScannerOptions.RESULT_FORMAT_JPEG
                )
            }

            if (maxPages > 0) {
                optionsBuilder.setPageLimit(maxPages.coerceAtMost(maxPagesLimit))
            }

            val scanner = GmsDocumentScanning.getClient(optionsBuilder.build())

            scanner.getStartScanIntent(activity)
                .addOnSuccessListener { intentSender ->
                    scannerLauncher?.launch(
                        IntentSenderRequest.Builder(intentSender).build()
                    )
                }
                .addOnFailureListener { e ->
                    Log.e(TAG, "Failed to start scanner", e)
                    val payload = JSONObject().put("error", e.localizedMessage ?: "Failed to start scanner")
                    dispatchEvent(
                        activity,
                        "Ikromjon\\DocumentScanner\\Events\\ScanFailed",
                        payload
                    )
                }

            return mapOf("success" to true)
        }
    }
}

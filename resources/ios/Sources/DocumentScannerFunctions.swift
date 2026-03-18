import Foundation
import VisionKit
import UIKit

// MARK: - Scanner Delegate

class DocumentScannerDelegate: NSObject, VNDocumentCameraViewControllerDelegate {
    static let shared = DocumentScannerDelegate()

    private var outputFormat: String = "jpeg"
    private var jpegQuality: CGFloat = 0.9
    private var storageDir: String = "scanned-documents"

    func configure(outputFormat: String, jpegQuality: Int, storageDir: String) {
        self.outputFormat = outputFormat
        self.jpegQuality = CGFloat(max(1, min(100, jpegQuality))) / 100.0
        self.storageDir = storageDir
    }

    private func storageDirectory() -> URL {
        let docs = FileManager.default.urls(for: .documentDirectory, in: .userDomainMask).first!
        let dir = docs.appendingPathComponent(storageDir, isDirectory: true)

        if !FileManager.default.fileExists(atPath: dir.path) {
            try? FileManager.default.createDirectory(at: dir, withIntermediateDirectories: true)
        }

        return dir
    }

    private func dispatchEvent(_ eventClass: String, _ payload: [String: Any]) {
        if let send = LaravelBridge.shared.send {
            send(eventClass, payload)
        }
    }

    // MARK: VNDocumentCameraViewControllerDelegate

    func documentCameraViewController(
        _ controller: VNDocumentCameraViewController,
        didFinishWith scan: VNDocumentCameraScan
    ) {
        controller.dismiss(animated: true)

        let dir = storageDirectory()
        let timestamp = Int(Date().timeIntervalSince1970 * 1000)
        var paths: [String] = []

        if outputFormat == "pdf" {
            let pdfPath = dir.appendingPathComponent("scan_\(timestamp).pdf")
            let pageSize = CGSize(width: 612, height: 792) // US Letter
            let renderer = UIGraphicsPDFRenderer(bounds: CGRect(origin: .zero, size: pageSize))

            let data = renderer.pdfData { context in
                for i in 0..<scan.pageCount {
                    context.beginPage()
                    let image = scan.imageOfPage(at: i)
                    let rect = CGRect(origin: .zero, size: pageSize)
                    image.draw(in: rect)
                }
            }

            try? data.write(to: pdfPath)
            paths.append(pdfPath.path)
        } else {
            for i in 0..<scan.pageCount {
                let image = scan.imageOfPage(at: i)
                let filePath = dir.appendingPathComponent("scan_\(timestamp)_\(i).jpg")

                if let jpegData = image.jpegData(compressionQuality: jpegQuality) {
                    try? jpegData.write(to: filePath)
                    paths.append(filePath.path)
                }
            }
        }

        dispatchEvent(
            "Ikromjon\\DocumentScanner\\Events\\DocumentScanned",
            [
                "paths": paths,
                "pageCount": scan.pageCount,
                "outputFormat": outputFormat,
            ]
        )
    }

    func documentCameraViewControllerDidCancel(_ controller: VNDocumentCameraViewController) {
        controller.dismiss(animated: true)

        dispatchEvent(
            "Ikromjon\\DocumentScanner\\Events\\ScanCancelled",
            [:]
        )
    }

    func documentCameraViewController(
        _ controller: VNDocumentCameraViewController,
        didFailWithError error: Error
    ) {
        controller.dismiss(animated: true)

        dispatchEvent(
            "Ikromjon\\DocumentScanner\\Events\\ScanFailed",
            ["error": error.localizedDescription]
        )
    }
}

// MARK: - Bridge Functions

enum DocumentScannerFunctions {

    class Scan: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            let config = parameters["_config"] as? [String: Any] ?? [:]
            let maxPages = parameters["maxPages"] as? Int
                ?? config["default_max_pages"] as? Int ?? 0
            let outputFormat = parameters["outputFormat"] as? String
                ?? config["default_output_format"] as? String ?? "jpeg"
            let jpegQuality = parameters["jpegQuality"] as? Int
                ?? config["default_jpeg_quality"] as? Int ?? 90
            let storageDir = config["storage_directory"] as? String ?? "scanned-documents"

            DocumentScannerDelegate.shared.configure(
                outputFormat: outputFormat,
                jpegQuality: jpegQuality,
                storageDir: storageDir
            )

            DispatchQueue.main.async {
                guard VNDocumentCameraViewController.isSupported else {
                    DocumentScannerDelegate.shared.documentCameraViewController(
                        VNDocumentCameraViewController(),
                        didFailWithError: NSError(
                            domain: "DocumentScanner",
                            code: -1,
                            userInfo: [NSLocalizedDescriptionKey: "Document scanning is not supported on this device"]
                        )
                    )
                    return
                }

                guard let rootVC = UIApplication.shared.connectedScenes
                    .compactMap({ $0 as? UIWindowScene })
                    .flatMap({ $0.windows })
                    .first(where: { $0.isKeyWindow })?.rootViewController else {
                    return
                }

                let scannerVC = VNDocumentCameraViewController()
                scannerVC.delegate = DocumentScannerDelegate.shared
                rootVC.present(scannerVC, animated: true)
            }

            return ["success": true]
        }
    }
}

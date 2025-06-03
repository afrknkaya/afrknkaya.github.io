<?php
header('Content-Type: application/json');
error_reporting(0); // Geliştirme sırasında E_ALL yapıp hataları görmek isteyebilirsiniz.

$response = ['success' => false, 'message' => 'Bilinmeyen bir hata oluştu.'];
$uploadDir = 'uploads/'; // Resimlerin bulunduğu klasör

// İsteğin POST olduğundan ve filePath parametresinin geldiğinden emin olalım
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $filePathToDelete = isset($input['filePath']) ? trim($input['filePath']) : '';

    if (!empty($filePathToDelete)) {
        // Güvenlik Kontrolü: Dosya yolunun beklenen $uploadDir içinde olduğundan emin ol.
        // Bu, ../ gibi ifadelerle başka klasörlerdeki dosyaların silinmesini engeller.
        $realUploadDir = realpath($uploadDir) . DIRECTORY_SEPARATOR;
        $realFilePathToDelete = realpath($filePathToDelete);

        if ($realFilePathToDelete && strpos($realFilePathToDelete, $realUploadDir) === 0) {
            if (file_exists($realFilePathToDelete)) {
                if (is_writable($realFilePathToDelete)) { // Dosyanın silinebilir olup olmadığını kontrol et
                    if (unlink($realFilePathToDelete)) {
                        $response['success'] = true;
                        $response['message'] = 'Resim dosyası başarıyla sunucudan silindi.';
                    } else {
                        $response['message'] = 'Resim dosyası sunucudan silinirken bir hata oluştu.';
                    }
                } else {
                     $response['message'] = 'Resim dosyası silinemiyor (yazma izni sorunu olabilir).';
                }
            } else {
                // Dosya zaten yoksa, bunu bir başarı olarak kabul edebiliriz veya farklı bir mesaj verebiliriz.
                $response['success'] = true; // Veya false, duruma göre
                $response['message'] = 'Silinecek resim dosyası sunucuda bulunamadı (zaten silinmiş olabilir).';
            }
        } else {
            $response['message'] = 'Geçersiz dosya yolu veya güvenlik ihlali girişimi.';
            // Log this attempt for security review
            // error_log("Security: Attempt to delete file outside upload directory: " . $filePathToDelete);
        }
    } else {
        $response['message'] = 'Silinecek dosya yolu belirtilmedi.';
    }
} else {
    $response['message'] = 'Geçersiz istek türü. Sadece POST istekleri kabul edilir.';
}

echo json_encode($response);
?>
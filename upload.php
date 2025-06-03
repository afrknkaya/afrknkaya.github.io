<?php
header('Content-Type: application/json'); // Yanıt tipini JSON olarak ayarla
error_reporting(0); // Hataları bastırabiliriz, ama geliştirme sırasında görmek iyi olur (E_ALL)

$response = ['success' => false, 'message' => '', 'filePath' => ''];
$uploadDir = 'uploads/'; // Resimlerin yükleneceği klasör (bu script'e göre göreceli)
$maxFileSize = 5 * 1024 * 1024; // Maksimum dosya boyutu 5MB
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// uploads klasörü yoksa oluşturmayı dene (sunucu izinlerine bağlı)
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0775, true)) { // İzinler 0775 veya 0777 olabilir (barındırmaya göre değişir)
        $response['message'] = 'Uploads klasörü oluşturulamadı. Lütfen manuel olarak oluşturun ve yazma izni verin.';
        echo json_encode($response);
        exit;
    }
}


if (isset($_FILES['itemImageFile'])) {
    $file = $_FILES['itemImageFile'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $phpFileUploadErrors = array(
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk.',
            8 => 'A PHP extension stopped the file upload.',
        );
        $response['message'] = isset($phpFileUploadErrors[$file['error']]) ? $phpFileUploadErrors[$file['error']] : 'Bilinmeyen bir yükleme hatası oluştu.';
        echo json_encode($response);
        exit;
    }

    if ($file['size'] > $maxFileSize) {
        $response['message'] = 'Dosya boyutu çok büyük. Maksimum ' . ($maxFileSize / 1024 / 1024) . 'MB olabilir.';
        echo json_encode($response);
        exit;
    }

    $fileInfo = getimagesize($file["tmp_name"]);
    if ($fileInfo === FALSE) {
        $response['message'] = "Geçersiz resim dosyası.";
        echo json_encode($response);
        exit;
    }
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    // MIME tipini de kontrol etmek daha güvenli olabilir ama uzantı kontrolü de bir seviyedir.
    // $finfo = finfo_open(FILEINFO_MIME_TYPE);
    // $mime = finfo_file($finfo, $file['tmp_name']);
    // finfo_close($finfo);
    // if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) { ... }

    if (!in_array($fileExtension, $allowedTypes)) {
        $response['message'] = 'Geçersiz dosya tipi. Sadece ' . implode(', ', $allowedTypes) . ' uzantılı dosyalar yüklenebilir.';
        echo json_encode($response);
        exit;
    }

    $newFileName = uniqid('img_', true) . '.' . $fileExtension;
    $uploadFilePath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        $response['success'] = true;
        $response['message'] = 'Resim başarıyla yüklendi.';
        $response['filePath'] = $uploadFilePath; // Göreceli yolu döndür (örn: uploads/img_xxxx.jpg)
    } else {
        $response['message'] = 'Dosya yüklenirken bir sunucu hatası oluştu. Uploads klasörünün yazılabilir olduğundan emin olun.';
    }
} else {
    $response['message'] = 'Yüklenecek dosya bulunamadı (itemImageFile POST verisi eksik).';
}

echo json_encode($response);
?>
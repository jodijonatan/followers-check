<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Followers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f7fafc;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .list-item {
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }
        .list-item:hover {
            transform: translateX(5px);
        }
        .custom-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            z-index: 1000;
        }
        .custom-notification.show {
            opacity: 1;
        }
    </style>
</head>
<body>

<?php
function ambilUsernames($file) {
    if (!file_exists($file)) {
        echo "<p class='text-red-500 font-bold'>File <b>$file</b> tidak ditemukan.</p>";
        return [];
    }
    $html = file_get_contents($file);
    $usernames = [];
    preg_match_all('/https:\/\/www\.instagram\.com\/([a-zA-Z0-9._]+)[\'"]/', $html, $hasil);
    if (isset($hasil[1])) {
        $usernames = array_unique($hasil[1]);
    }
    return $usernames;
}
$followers = ambilUsernames('followers_1.html');
$followings = ambilUsernames('following.html');
$not_following_back = array_diff($followings, $followers);
$not_followed_back = array_diff($followers, $followings);
$mutuals = array_intersect($followers, $followings);

function tampilkanList($judul, $data, $ikon = '', $color = '') {
    $itemColor = ($color === 'red') ? 'text-red-500' : (($color === 'green') ? 'text-green-500' : 'text-blue-500');
    
    echo "<div class='mb-6'>";
    echo "<h2 class='text-2xl font-bold mb-4 flex items-center'><span class='mr-2 $itemColor'>$ikon</span> $judul <span class='ml-2 text-lg font-normal text-gray-500'>(" . count($data) . ")</span></h2>";
    
    if (count($data) > 0) {
        echo "<ul class='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2'>";
        foreach ($data as $user) {
            echo "<li class='list-item p-2 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200' onclick='copyToClipboard(this)'>";
            echo "<span class='font-medium $itemColor'>$user</span>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='text-gray-500'>Tidak ada data.</p>";
    }
    echo "</div>";
}
?>

<div class="container">
    <header class="text-center mb-8">
        <h1 class="text-4xl font-extrabold text-blue-600 mb-2">📊 Analisis Followers Instagram</h1>
        <p class="text-lg text-gray-600">Laporan untuk akun <b>cold.joo</b></p>
    </header>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center mb-8">
        <div class="p-6 bg-blue-50 rounded-lg shadow">
            <h2 class="text-3xl font-bold text-blue-600"><?= count($followers) ?></h2>
            <p class="text-gray-500">Total Followers</p>
        </div>
        <div class="p-6 bg-green-50 rounded-lg shadow">
            <h2 class="text-3xl font-bold text-green-600"><?= count($followings) ?></h2>
            <p class="text-gray-500">Total Following</p>
        </div>
    </div>
    <main class="space-y-8">
        <?php
        tampilkanList('Yang tidak follback kamu', $not_following_back, '🔻', 'red');
        tampilkanList('Akun yang tidak kamu follback', $not_followed_back, '🔺', 'blue');
        tampilkanList('Saling mengikuti (Mutuals)', $mutuals, '✅', 'green');
        ?>
    </main>
</div>

<div id="custom-notification" class="custom-notification"></div>

<script>
    function copyToClipboard(element) {
        const username = element.querySelector('span').innerText;
        navigator.clipboard.writeText(username).then(() => {
            showNotification(`Username **${username}** berhasil disalin!`);
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
        });
    }

    function showNotification(message) {
        const notification = document.getElementById('custom-notification');
        notification.innerHTML = message;
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 2000);
    }
</script>

</body>
</html>
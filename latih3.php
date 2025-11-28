<!DOCTYPE html>
<html>
    <head>
        <title>Test Penyisipan PHP Pada HTML</title>
    </head>
    <body>
    <!-- Identitas: 06860 -->
    Kapal Asing, Silakan identifikasikan diri Anda! <br>
    <?php
    // Berikut ini adalah inisiasi beberapa variabel
    $namad = "Bejo";
    $namat = "Noto";
    $namab = "Negoro";
    ?>
    <b>Ini adalah kapal Federasi Planet USS Enterprise.<br>
    <?php
        print("Saya $namad,$namat,$namab,kapten kapal.</b>");
        print $namad,$namat, $namab;
        echo "<br>";
        echo "saya", $namad,$namat,$namab;
    ?>
    </body>
</html>
<!-- Analisa: Baris 17 (print $namad,$namat, $namab;) akan menyebabkan error karena fungsi print() dalam PHP hanya menerima satu argumen, sedangkan di sini diberikan tiga argumen dipisahkan koma. Sebaliknya, echo dapat menerima banyak argumen. -->

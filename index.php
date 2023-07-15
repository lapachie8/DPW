<?php
session_start();
include 'connect.php';

// Fungsi untuk melakukan peminjaman
function beliProduk($bukuId, $quantity)
{
    global $conn;
    $userId = $_SESSION['user_id'];

    // Mendapatkan data produk
    $query = "SELECT * FROM produk WHERE produk_id = $bukuId";
    $result = $conn->query($query);
    $produk = $result->fetch_assoc();

    // Memastikan stok cukup untuk pembelian
    if ($produk['stok'] >= $quantity) {
        $harga = $produk['harga'];
        $totalHarga = $harga * $quantity;

        // Mengurangi stok barang
        $newStok = $produk['stok'] - $quantity;
        $updateQuery = "UPDATE produk SET stok = $newStok WHERE produk_id = $bukuId";
        $conn->query($updateQuery);

        // Menyimpan data transaksi
        $insertQuery = "INSERT INTO transaksi (user_id, produk_id, quantity, tanggal, total_harga) VALUES ($userId, $bukuId, $quantity, NOW(), $totalHarga)";
        $conn->query($insertQuery);

        // Menampilkan alert pembelian berhasil menggunakan JavaScript
        echo '<script>alert("Pembelian berhasil!");</script>';
    } else {
        // Menampilkan alert stok tidak mencukupi menggunakan JavaScript
        echo '<script>alert("Stok tidak mencukupi!");</script>';
    }
}


    // Proses pembelian saat mengirim form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['produk_id']) && isset($_POST['quantity'])) {
            $bukuId = $_POST['produk_id'];
            $quantity = $_POST['quantity'];
            beliProduk($bukuId, $quantity);
        }
    }
    $query = "SELECT * FROM produk";
    $result = $conn->query($query);
    $produks = $result->fetch_all(MYSQLI_ASSOC);
  ?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>rafff's e-commerce</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="index.php">
        <img src="heker.jpg" alt="Logo" width="200" height="50">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li> <?php
            if (isset($_SESSION["email"])) {
                echo '
								
									<li class="nav-item">
										<a class="nav-link" href="logout.php">Selamat Datang, ' . $_SESSION["email"] . '</a>
									</li>';
            } else {
                echo '
								
									<li class="nav-item">
										<a class="nav-link" href="login.php">Login</a>
									</li>';
            }
            ?> <li class="nav-item">
            <a class="nav-link" href="index.php">Product</a>
          </li>
        </ul>
      </div>
    </nav>
    <!-- Card Produk -->
    <!-- Card Produk -->
    <div class="container mt-5">
        <div class="row">
            <?php foreach ($produks as $produk): ?>
                <div class="col-md-4">
                    <div class="card">
                    <img src="https://picsum.photos/150/150" class="card-img-top" alt="Gambar Produk 3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $produk['nama']; ?></h5>
                            <p class="card-text">Harga: <?php echo $produk['harga']; ?></p>
                            <p class="card-text">Stok: <?php echo $produk['stok']; ?></p>
                            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="produk_id" value="<?php echo $produk['produk_id']; ?>">
                                <div class="form-group">
                                    <label for="quantity<?php echo $produk['produk_id']; ?>">Quantity</label>
                                    <input required type="number" class="form-control" id="quantity<?php echo $produk['produk_id']; ?>"
                                          min="1" name="quantity" value="1">
                                </div>
                                <button type="submit" class="btn btn-primary">Beli</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  </body>
</html>
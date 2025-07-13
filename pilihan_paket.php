<?php include 'header.php'; ?>
<title>Pilih Paket - Classic Coffee 789</title>
<style>
    .choice-container { text-align: center; margin: 50px auto; max-width: 800px; }
    .choice-grid { display: flex; gap: 30px; justify-content: center; margin-top: 30px; }
    .choice-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-decoration: none; color: #333; transition: all 0.3s ease; flex: 1; }
    .choice-card:hover { transform: translateY(-10px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
    .choice-card i { font-size: 4em; color: #a0522d; margin-bottom: 20px; }
    .choice-card h2 { font-size: 1.5em; }
</style>

<div class="choice-container">
    <h1>Pilih Jenis Paket</h1>
    <div class="choice-grid">
        <a href="paket.php?kategori=kopi" class="choice-card">
            <i class="fas fa-coffee"></i>
            <h2>Paket Kopi Original</h2>
        </a>
        <a href="paket.php?kategori=teh" class="choice-card">
            <i class="fas fa-leaf"></i>
            <h2>Paket Thai Tea</h2>
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
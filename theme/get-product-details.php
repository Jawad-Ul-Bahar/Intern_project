<?php
include("connection.php");

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $q = mysqli_query($con, "SELECT * FROM products WHERE id = $id");
    if ($row = mysqli_fetch_assoc($q)) {
?>
    <div style="padding: 15px; max-width: 200px; margin: auto;">
        <h4 style="margin-bottom: 10px; font-size: 18px; font-weight: bold; color: #333;">
            <?= $row['name'] ?>
        </h4>

        <div style="width: 100%; height: auto; overflow: hidden; border-radius: 8px; margin-bottom: 12px;">
            <img src='../adminpanel3/img/<?= $row['image'] ?>' style='width: 100%; object-fit: cover;'>
        </div>

        <p style="margin: 0 0 5px; font-size: 15px; color: #444;">
            <strong>Price:</strong> Rs:<?= $row['price'] ?>
        </p>

        <?php if (!empty($row['shirt_type'])): ?>
        <p style="margin: 0 0 5px; font-size: 14px; color: #444;">
            <strong>Shirt Type:</strong> <?= $row['shirt_type'] ?>
        </p>
        <?php endif; ?>

        <p style="margin: 0 0 15px; font-size: 13px; color: #666;">
            <?= $row['description'] ?>
        </p>

        <a href="product-detail.php?proid=<?= $row['id'] ?>"
           style="display: inline-block; width: 100%; padding: 10px; background: #310E68 !important; border-radius: 50px; color: white; text-align: center; text-decoration: none; border-radius: 5px; font-size: 14px; cursor: pointer;">
           Add to Cart
        </a>
    </div>
<?php
    } else {
        echo "Product not found.";
    }
}
?>

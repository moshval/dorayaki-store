<?php include('../util/buy_util.php');?>
<?php
$_SESSION['is_admin'] = 1;

?>
<?php
    if (isset($_GET['id_dorayaki'])) {
        $dora_id = htmlspecialchars(trim($_GET['id_dorayaki']));
        if(filter_var($dora_id, FILTER_VALIDATE_INT) or $dora_id == 0){
            $item_info = getItem($dora_id);
            if(!empty($item_info)){
                $found = true;
            }
            else{
                $found = false;
            }
        }
        else{
            $found = false;
        }
    } else {
        $found = false;
    }

?>

<!-- Buy Product (user), Modify Stock (admin) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/details.css">
    <script src=""></script>
    <title>Dorayaki Details</title>
</head>

<body>
    <?php include('../util/header.php'); ?>
    <?php if($found){?>
    <div class="details">
        <div class="img-frame">
            <img src="../<?php echo $item_info[0]["img_source"];?>" alt="" class="prod-img">
        </div>
        <div class="text">
            <h3 style="font-size:x-large"><?php echo $item_info[0]["nama"];?></h3>
            <h3 style="font-size:large;color: #C4161C ">Rp<?php echo number_format($item_info[0]["price"]);?></h3>
            <p>Amount Sold : <?php echo $item_info[0]["total_buy"];?></p>
            <p>Amount Remaining : <?php echo $item_info[0]["amount"];?></p>
            <p><?php echo $item_info[0]["description"];?></p>
        </div>
        <div>
            <?php buttonAdder($dora_id); ?>
        </div>
    </div>
    <?php }
        else{
            echo '<h3 style="font-size:x-large">Page not Found :P</h3>';
        }
        ?>
</body>
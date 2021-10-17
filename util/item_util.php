<?php
    function getItem($itemid)
    {
        $db = new SQLite3('../db/basdat.db');
        // $sql = "SELECT t.id_dorayaki,sum(total_buy) as total_buy,nama,d.price, description,amount,img_source FROM transactions t inner join dorayaki d ON t.id_dorayaki = d.id_dorayaki WHERE t.id_dorayaki =$itemid group by t.id_dorayaki order by total_buy desc";
        // $sql = "SELECT d.id_dorayaki,sum(total_buy) as total_buy, nama, d.price, description,amount,img_source from dorayaki d left outer join transactions t ON t.id_dorayaki = d.id_dorayaki WHERE d.id_dorayaki =$itemid group by t.id_dorayaki order by total_buy desc";
        $sql = "SELECT id_dorayaki, nama, price, description, amount,img_source from dorayaki WHERE id_dorayaki = $itemid";
        $dorayakis = [];
        $results = $db->query($sql);
        while ($res = $results->fetchArray(1)) {
            array_push($dorayakis, $res);
        }
        if(!empty($dorayakis)){
            $dorayakis[0]["total_buy"] = getAmountSold($itemid);
        }
        $db->close();
        return $dorayakis;
    }

    function buttonAdder($id)
    {
        if ($_SESSION['is_admin']) {
            echo '<a href="edit_amount.php?id_dorayaki='. $id . '"><button class="prod-edit" onclick="" style="cursor:pointer">Change Amount</button></a>';
            echo '<a href="edit_variant.php?id_dorayaki='. $id . '"><button class="prod-edit" id="editvar" onclick="" style="cursor:pointer">Edit Variant</button></a>';
            echo '<a href="prod_history.php?id_dorayaki='. $id . '"><button class="prod-edit" id="hist" onclick="" style="cursor:pointer">History</button></a>';
            echo '<button class="prod-delete" onclick="deletePrompt('.$id.');" style="cursor:pointer">Delete Variant</button>';
        } else if (!$_SESSION['is_admin']) {    
            echo '<a href="edit_amount.php?id_dorayaki='. $id . '"><button class="prod-buy" onclick="" style="cursor:pointer">Buy</button></a>';
        }
    }
    
    function buyAdder($id){
        if ($_SESSION['is_admin']) {
            echo '<button class="butbuy" name="buyProd" type="submit">Buy</button>';
        } else if (!$_SESSION['is_admin']) {
            echo '<button class="butbuy" name="buyProd" type="submit">Buy</button>';
        }
    }

    function buyProduct($id){
        if (isset($_POST['buyProd'])) {
            $db = new SQLite3('../db/basdat.db');
            $sql = "UPDATE dorayaki SET amount = amount-1 WHERE id_dorayaki=$id";
            $ret = $db->exec($sql);
            $db->close();
        }
    }

    function getAmountSold($itemid){
        $db = new SQLite3('../db/basdat.db');
        $sql = "SELECT id_dorayaki from transactions WHERE id_dorayaki = $itemid";
        $transaction = [];
        $results = $db->query($sql);
        while ($res = $results->fetchArray(1)) {
            array_push($transaction, $res);
        }
        $db->close();
        $sold = count($transaction);
        return $sold;

    }

    function checkExt(){
    if (isset($_FILES['addimage'])) {
        $tmp = explode('.', $_FILES['addimage']['name']);
        $imgext = strtolower(end($tmp));
        $allowedext = array("png", "jpg", "jpeg");
        if (in_array($imgext, $allowedext) === false) {
            echo '<span style="color:#C4161C;">Failed. Allowed Extensions : png, jpg, jpeg</span>';
        }
    }
}

    function submitImg($isNew){
        if (isset($_POST['AddVar'])) {
            $name = $_POST['addname'];
            $price = $_POST['addprice'];
            $initstock = $_POST['initstock'];
            $desc = $_POST['adddesc'];
            if (isset($_FILES['addimage'])) {
                $errors = array();
                $imgname = $_FILES['addimage']['name'];
                $imgtmp = $_FILES['addimage']['tmp_name'];
                $imgtype = $_FILES['addimage']['type'];
                $tmp = explode('.', $_FILES['addimage']['name']);
                $imgext = strtolower(end($tmp));
                $allowedext = array("png", "jpg", "jpeg");
                if (in_array($imgext, $allowedext) === false) {
                    $errors[] = "Failed";
                }

                if (empty($errors) == true) {
                    $db = new SQLite3('../db/basdat.db');
                    $query = "SELECT `nama` FROM `dorayaki` WHERE  `nama` ='". $name . "';";
                    $res = $db->query($query);
                    $rws = $res->fetchArray();
                    if($isNew){
                        if(!$rws){
                            $imgpath = "img/" . $imgname;
                            $sql = "INSERT INTO dorayaki(nama,price,amount,description,img_source) VALUES (:name,:price,:initstock,:desc,:imgname);"; 
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(":name",$name);
                            $stmt->bindParam(":price",$price);
                            $stmt->bindParam(":initstock",$initstock);
                            $stmt->bindParam(":desc",$desc);
                            $stmt->bindParam(":imgname",$imgpath);
                            $stmt->execute();
                            move_uploaded_file($imgtmp, "../img/" . $imgname);
                            echo '<span style="color:#33b864;">Success. Variant Added</span>';
                        }
                        else{
                            echo '<span style="color:#C4161C;">Failed. Duplicate Product Name.</span>';
                        }
                    }
                    else{
                        $curr_id = $_GET['id_dorayaki'];
                        $imgpath = "img/" . $imgname;
                        $sql = "UPDATE dorayaki SET nama = :name, price = :price, amount = :initstock, description = :desc,img_source = :imgname WHERE `id_dorayaki` = '". $curr_id ."';";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(":name",$name);
                        $stmt->bindParam(":price",$price);
                        $stmt->bindParam(":initstock",$initstock);
                        $stmt->bindParam(":desc",$desc);
                        $stmt->bindParam(":imgname",$imgpath);
                        $stmt->execute();
                        move_uploaded_file($imgtmp, "../img/" . $imgname);
                        echo '<span style="color:#33b864;">Success. Variant Edited</span>';
                    }
                }
                
            }
        }
    }

 ?>
<?php require_once 'includes/header.php';

// Include the qrlib file 
require_once './phpqrcode/qrlib.php';


$sql = "SELECT * FROM product WHERE status = 1";
$query = $connect->query($sql);
$countProduct = $query->num_rows;

$orderSql = "SELECT * FROM orders WHERE order_status = 1";
$orderQuery = $connect->query($orderSql);
$countOrder = $orderQuery->num_rows;

$totalRevenue = 0.0;
while ($orderResult = $orderQuery->fetch_assoc()) {
	$totalRevenue += $orderResult['paid'];
}

// To get lowstocks
$lowStockSql = "SELECT * FROM product WHERE quantity <= 3 AND status = 1";
$lowStockQuery = $connect->query($lowStockSql);
$countLowStock = $lowStockQuery->num_rows;

// To get amount of products remaining in the product table
$products_array = array();
$query = "SELECT product_name, quantity FROM product WHERE status = 1";
$products = $connect->query($query);
if ($products->num_rows > 0) {
	while($row = $products->fetch_assoc()) {
		$product = [
			"Product Name" => $row['product_name'],
			"Quantity" => $row['quantity']
		];
		array_push($products_array, $product);
	}
}

$image = "";
foreach($products_array as $prod) {
	$pro =  $prod['Product Name']. " = " .$prod['Quantity'] .", ";
    $image .= $pro;
}

$path = 'includes/'; 
$file = $path.uniqid().".png"; 

// $ecc stores error correction capability('L') 
$ecc = 'H'; 
// $pixel_Size = 20; 
// $frame_Size = ; 

// Generates QR Code and Stores it in directory given 
QRcode::png($image, $file, $ecc); 


// To get user wise orders
$userwisesql = "SELECT users.username , SUM(orders.grand_total) as totalorder 
				FROM orders 
				INNER JOIN users ON orders.user_id = users.user_id 
				WHERE orders.order_status = 1 GROUP BY orders.user_id";
$userwiseQuery = $connect->query($userwisesql);
$userwieseOrder = $userwiseQuery->num_rows;

$connect->close();

?>


<style type="text/css">
.ui-datepicker-calendar {
    display: none;
}
</style>

<!-- fullCalendar 2.2.5-->
<link rel="stylesheet" href="assests/plugins/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="assests/plugins/fullcalendar/fullcalendar.print.css" media="print">


<div class="row">
    <?php  if(isset($_SESSION['userId']) && $_SESSION['userId']==1) { ?>
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-heading">

                <a href="product.php" style="text-decoration:none;color:black;">
                    Total Product
                    <span class="badge pull pull-right"><?php echo number_format($countProduct); ?></span>
                </a>

            </div>
            <!--/panel-hdeaing-->
        </div>
        <!--/panel-->
    </div>
    <!--/col-md-4-->

    <div class="col-md-4">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <a href="product.php" style="text-decoration:none;color:black;">
                    Low Stock
                    <span class="badge pull pull-right"><?php echo $countLowStock; ?></span>
                </a>

            </div>
            <!--/panel-hdeaing-->
        </div>
        <!--/panel-->
    </div>
    <!--/col-md-4-->


    <?php } ?>
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading">
                <a href="orders.php?o=manord" style="text-decoration:none;color:black;">
                    Total Orders
                    <span class="badge pull pull-right"><?php echo number_format($countOrder); ?></span>
                </a>

            </div>
            <!--/panel-hdeaing-->
        </div>
        <!--/panel-->
    </div>
    <!--/col-md-4-->



    <div class="col-md-4">
        <div class="card">
            <div class="cardHeader">
                <h3><?php echo date('d'); ?></h3>
            </div>

            <div class="cardContainer">
                <p><?php echo date('l') .' '.date('d').', '.date('Y'); ?></p>
            </div>
        </div>
        <br />

        <div class="card">
            <div class="cardHeader" style="background-color:#245580;">
                <h3>
                    <?php 
                        if($totalRevenue) {
                            echo "₦". number_format($totalRevenue);
                        } else {
                            echo '0';
                            } 
                    ?>
                </h3>
            </div>

            <div class="cardContainer">
                <p> Total Revenue</p>
            </div>
        </div> <br>
        <!-- Products QRcode implementation-->
        <div class="card">
            <div class="" style="background-color: white;">
                <?php echo "<img src='".$file."' style='width: 150px;'>"; ?>
            </div>
        </div>
    </div>

    <?php  if(isset($_SESSION['userId']) && $_SESSION['userId']==1) { ?>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading"> <i class="glyphicon glyphicon-calendar"></i> User Wise Order</div>
            <div class="panel-body">
                <table class="table" id="productTable">
                    <thead>
                        <tr>
                            <th style="width:40%;">Name</th>
                            <th style="width:20%;">Orders In Naira</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($orderResult = $userwiseQuery->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $orderResult['username']?></td>
                            <td><?php echo "₦". number_format($orderResult['totalorder'])?></td>

                        </tr>

                        <?php } ?>
                    </tbody>
                </table>
                <!--<div id="calendar"></div>-->
            </div>
        </div>

    </div>
    <?php  } ?>

</div>
<!--/row-->

<!-- fullCalendar 2.2.5 -->
<script src="assests/plugins/moment/moment.min.js"></script>
<script src="assests/plugins/fullcalendar/fullcalendar.min.js"></script>


<script type="text/javascript">
$(function() {
    // top bar active
    $('#navDashboard').addClass('active');

    //Date for the calendar events (dummy data)
    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();

    $('#calendar').fullCalendar({
        header: {
            left: '',
            center: 'title'
        },
        buttonText: {
            today: 'today',
            month: 'month'
        }
    });


});
</script>

<?php require_once 'includes/footer.php'; ?>
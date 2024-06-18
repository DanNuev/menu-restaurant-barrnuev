<?php
session_start();

require_once("clase.php");

$usar_db = new DBControl();

if (!empty($_GET["accion"])) {
	switch ($_GET["accion"]) {
		case "agregar":
			if (!empty($_POST["txtcantidad"])) {
				$codproducto = $usar_db->vaiQuery("SELECT * FROM productos WHERE cod='" . $_GET["cod"] . "'");
				$items_array = array($codproducto[0]["cod"] => array(
					'vai_nom'		=> $codproducto[0]["nom"],
					'vai_cod'		=> $codproducto[0]["cod"],
					'txtcantidad'	=> $_POST["txtcantidad"],
					'vai_pre'		=> $codproducto[0]["pre"],
					'vai_img'		=> $codproducto[0]["img"]
				));

				if (!empty($_SESSION["items_carrito"])) {
					if (in_array(
						$codproducto[0]["cod"],
						array_keys($_SESSION["items_carrito"])
					)) {
						foreach ($_SESSION["items_carrito"] as $i => $j) {
							if ($codproducto[0]["cod"] == $i) {
								if (empty($_SESSION["items_carrito"][$i]["txtcantidad"])) {
									$_SESSION["items_carrito"][$i]["txtcantidad"] = 0;
								}
								$_SESSION["items_carrito"][$i]["txtcantidad"] += $_POST["txtcantidad"];
							}
						}
					} else {
						$_SESSION["items_carrito"] = array_merge($_SESSION["items_carrito"], $items_array);
					}
				} else {
					$_SESSION["items_carrito"] = $items_array;
				}
			}
			break;
		case "eliminar":
			if (!empty($_SESSION["items_carrito"])) {
				foreach ($_SESSION["items_carrito"] as $i => $j) {
					if ($_GET["eliminarcode"] == $i) {
						unset($_SESSION["items_carrito"][$i]);
					}
					if (empty($_SESSION["items_carrito"])) {
						unset($_SESSION["items_carrito"]);
					}
				}
			}
			break;
		case "vacio":
			unset($_SESSION["items_carrito"]);
			break;
		case "pagar":
			echo "<script> alert('Gracias por su compra');window.location= 'index.php' </script>";
			unset($_SESSION["items_carrito"]);
			break;
	}
}

?>
<!DOCTYPE html>
<html>


<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
	<title>4AMP</title>
	<link href="css/style.css" rel="stylesheet" />
</head>

<body>
	<header>
		<h1>Tienda</h1>
		<div class="cart">
			<div class="inner-car">
				<i class="uil uil-shopping-cart"></i>
				<i class="uil uil-user" onclick="window.location.href = 'login.php'"></i>
				<div>
					<?php
					if (isset($_SESSION["items_carrito"])) {
						$totcantidad = 0;
						$totprecio = 0;
					?>
						<div class="inner-cart hidden">
							<span id="title-cart">Carrito</span>
							<?php
							foreach ($_SESSION["items_carrito"] as $item) {
								$item_price = $item["txtcantidad"] * $item["vai_pre"];
							?>
								<div class="producto">
									<div class="pro-img">
										<img src="<?php echo $item["vai_img"]; ?>" class="imagen_peque" />
										<?php echo $item["vai_cod"]; ?>
									</div>
									<div class="info">
										<div class="pro-desc">
											<h3><?php echo $item["vai_nom"]; ?></h3>
											<span>Precio unitario: <?php echo "$ " . $item["vai_pre"]; ?></span><br>
											<span>Cantidad: <?php echo $item["txtcantidad"]; ?></span><br>
											<span>Precio: <?php echo "$ " . number_format($item_price, 2); ?></span><br>
										</div>
										<div class="elimBtn">
											<a href="index.php?accion=eliminar&eliminarcode=<?php echo $item["vai_cod"]; ?>"><i class="uil uil-trash-alt"></i></a>
										</div>
									</div>
								</div>
								<hr>

							<?php
								$totcantidad += $item["txtcantidad"];
								$totprecio += ($item["vai_pre"] * $item["txtcantidad"]);
							}
							?>
							<div class="total">
								<span><strong>Total de productos: <?php echo $totcantidad; ?></strong></span><br>
								<span>Total a pagar: <?php echo "$ " . number_format($totprecio, 2); ?></span><br><br>
								<button class="pagar" onclick="window.location = 'index.php?accion=vacio';"><a>Borrar pedido</a></button>
								<button class="pagar" onclick="window.location = 'index.php?accion=pagar';"><a>Pagar</a></button>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</header>

	<div class="main">
		<div align="center">
			<h2>Productos</h2>
		</div>
		<div class="contenedor_general">
			<?php
			/*4AMP*/
			$productos_array = $usar_db->vaiquery("SELECT * FROM productos ORDER BY id ASC");
			if (!empty($productos_array)) {
				foreach ($productos_array as $i => $k) {
			?>
					<div class="contenedor_productos">
						<form method="POST" action="index.php?accion=agregar&cod=<?php echo $productos_array[$i]["cod"]; ?>">
							<div><img src="<?php echo $productos_array[$i]["img"]; ?>"></div>
							<div>
								<div style="padding-top:20px;font-size:18px;"><?php echo $productos_array[$i]["nom"]; ?></div>
								<div style="padding-top:10px;font-size:20px;"><?php echo "$" . $productos_array[$i]["pre"]; ?>
								</div>
								<div><input type="text" name="txtcantidad" name="cant" oninput="this.value = this.value.replace(/[^0-9]/, '');" value="1" size="2" />
									<button onclick="this.form.submit();">Agregar</button>
								</div>
							</div>
						</form>
					</div>
			<?php
				}
			}
			?>
		</div>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', () => {

			function regNum(form) {
				form.cant.value = form.cant.value.replace(/[^0-9]/, '');
			}
			const cart = document.querySelector(".uil-shopping-cart"),
				modalBtn = [add = document.querySelector(".add"), closeModal = document.querySelector(".cerrar-modal")];

			const menu = document.querySelector(".inner-cart"),
				modal = document.querySelector(".container-modal");

			let bool = [false, false];

			cart.addEventListener('click', () => {
				if (menu == null) {
					alert("Aun no se ha agregado nada al carrito");
				} else if (bool[0]) {
					menu.classList.add('hidden');
					bool[0] = false;
				} else {
					menu.classList.remove('hidden');
					bool[0] = true;
				}

			});


		});
	</script>
</body>

</html>
<?php

?>
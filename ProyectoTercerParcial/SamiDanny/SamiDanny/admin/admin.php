<?php
session_start();

require_once("../clase.php");

$usar_db = new DBControl();

$hidd = false;

if (!empty($_GET['id'])) {
	$hidd = false;
	$id = $_GET['id'];
	$sql = "SELECT nom, pre FROM productos WHERE id = '$id'";
	$result = $usar_db->vaiQuery($sql);
} else {
	$hidd = true;
}


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
			echo "<script> alert('Gracias por su compra');window.location= 'admin.php' </script>";
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
	<link href="../css/style.css" rel="stylesheet" />
</head>

<body>
	<header>
		<h1>Tienda</h1>
		<div class="cart">
			<div class="inner-car">
				<i class="uil uil-shopping-cart"></i>
				<i class="uil uil-signout" onclick="window.location.href = 'cerrar_sesion.php'"></i>
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
										<img src="../<?php echo $item["vai_img"]; ?>" class="imagen_peque" />
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
											<a href="admin.php?accion=eliminar&eliminarcode=<?php echo $item["vai_cod"]; ?>"><i class="uil uil-trash-alt"></i></a>
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
								<button class="pagar" onclick="window.location = 'admin.php?accion=vacio';"><a>Borrar pedido</a></button>
								<button class="pagar" onclick="window.location = 'admin.php?accion=pagar';"><a>Pagar</a></button>
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
						<form method="POST" action="admin.php?accion=agregar&cod=<?php echo $productos_array[$i]["cod"]; ?>">
							<div class="elimBtn">
								<?php echo "<a href='admin.php?id=" . $productos_array[$i]['id'] . "' ><i class='uil uil-edit'></i></a>" ?>
								<?php echo "<a href='productos_eliminar.php?id=" . $productos_array[$i]['id'] . "' onClick=\"return confirm('¿Estás seguro de eliminar el producto?')\" ><i class='uil uil-trash-alt'></i></a>" ?>
							</div>
							<div><img src="../<?php echo $productos_array[$i]["img"]; ?>"></div>
							<div>
								<div style="padding-top:20px;font-size:18px;"><?php echo $productos_array[$i]["nom"]; ?></div>
								<div style="padding-top:10px;font-size:20px;"><?php echo "$" . $productos_array[$i]["pre"]; ?>
								</div>
								<div>
									<input type="text" name="txtcantidad" name="cant" oninput="this.value = this.value.replace(/[^0-9]/, '');" value="1" size="2" />
									<button onclick="this.form.submit();">Agregar</button>
								</div>
							</div>
						</form>
					</div>
			<?php
				}
			}
			?>
			<div class="contenedor_productos add">
				<i class="uil uil-plus"></i>
			</div>
			<div class="container-modal hidden">
				<div class="btn-cerrar">
					<label for="btn-modal" class="lbl-modal">
						<i class="uil uil-multiply"></i>
					</label>
				</div>
				<!-- ************************* Contedido de la ventana modal para agregar productos nuevos ************************* -->
				<div class="content-modal">
					<div class="contenedor_productos productos_modal">
						<form action="agregarPro.php" method="POST" enctype="multipart/form-data">
							<label for="name">Nombre:</label><br>
							<input type="text" class="name" name="name" id="name" required><br><br>

							<label for="preci">Precio:</label><br>
							<input type="number" name="preci" id="preci" required><br><br>

							<label for="img">Imagen:</label><br>
							<input type="file" name="img" id="img" required><br><br>

							<button name="regisDatos" onclick="this.form.submit();">Enviar datos</button>
						</form>
					</div>
					<label for="btn-modal" class="cerrar-modal lbl-modal"></label>
				</div>
			</div>

			<!-- ****************************Contenedor modal modificar******************************-->

			<div class="container-modal 1 <?php if ($hidd)  echo "hidden"; ?>">
				<div class="btn-cerrar">
					<label for="btn-modal" class="lbl-modal"></label>
				</div>
				<div class="content-modal">
					<div class="contenedor_productos productos_modal">
						<form action="productos_modif.php" method="POST" enctype="multipart/form-data">
							<label for="nom">Nombre:</label><br>
							<input type="text" class="name" name="nom" id="nom" value="<?php echo $result[0]['nom']; ?>" required><br><br>

							<label for="pre">Precio:</label><br>
							<input type="number" name="pre" id="preci" value="<?php echo $result[0]['pre']; ?>" required><br><br>

							<label for="image">Imagen:</label><br>
							<input type="file" name="image" id="image" required><br><br>

							<input type="hidden" name="id" value="<?php echo $id ?>">

							<button name="modifDatos" onclick="this.form.submit();">Enviar Datos</button>
						</form>
					</div>
					<label for="btn-modal" class="cerrar-modal lbl-modal modif"></label>
				</div>
			</div>
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

			const cerrarModif = document.querySelector(".modif");

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

			modalBtn.forEach(i => {
				console.log("xd");
				i.addEventListener('click', () => {
					if (bool[1]) {

						modal.classList.add('hidden');
						bool[1] = false;
					} else {
						console.log("xd");
						modal.classList.remove('hidden');
						bool[1] = true;
					}
				})
			});

			console.log(cerrarModif)

			cerrarModif.addEventListener('click', () => {
				window.location.href = 'admin.php'
			});
		});
	</script>
</body>

</html>
<?php

?>
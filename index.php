<?php 

	class scramble {
		
		var $block_w = 10;
		var $block_h = 10;
		var $prefix = "shuffled_";

	
		function __construct($imgname) {
			
			$this->imgname = $imgname;

			$size = @getimagesize($imgname);
			$this->mime = $size["mime"];
			switch($this->mime){
				case "image/jpeg":
					$im = imagecreatefromjpeg($imgname); //jpeg file
					break;
				case "image/gif":
					$im = imagecreatefromgif($imgname); //gif file
					break;
				case "image/png":
					$im = imagecreatefrompng($imgname); //png file
					break;
				default: 
					$im  = imagecreatetruecolor(150, 30);
					$bgc = imagecolorallocate($im, 255, 255, 255);
					$tc  = imagecolorallocate($im, 0, 0, 0);
					
					imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

					imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
			}

			$this->image = $im;
			$this->image_w = $size[0];
			$this->image_h = $size[1];

			$this->block_w_mod = $this->image_w % $this->block_w;
			$this->block_h_mod = $this->image_h % $this->block_h;
		}


		public function showImage(){
			header('Content-Type: image/jpeg');
			imagejpeg($this->image);
			imagedestroy($this->image);
		}

		public function showShuffled(){
			echo "<img src='".$this->prefix.$this->imgname."' alt=''>";
		}

		public function scramble(){

			$grid = array();

			$cols = floor($this->image_w / $this->block_w);
			$rows = floor($this->image_h / $this->block_h);
			
			for ($row=0; $row < $rows; $row++) { 
				for ($col=0; $col < $cols; $col++) { 

					// if our image doesn't divide evenly into
					// our block size, some blocks might have to be slightly

					$width_mod = 0;
					if($col == $cols - 1 && $this->block_w_mod > 0){	
						$width_mod = $this->block_w_mod;
					}
					$height_mod = 0;
					if($row == $rows - 1 && $this->block_h_mod > 0){
						$height_mod = $this->block_h_mod;
					}

					$grid[$row][] = array(
						"row"     => $row,
						"col"     => $col,
						"width"   => $this->block_w + $width_mod,
						"height"  => $this->block_h + $height_mod,
						"x"		  => $col * $this->block_w,
						"y"       => $row * $this->block_h
					);

				}
			}

			$im2 = ImageCreateTrueColor($this->image_w, $this->image_h);

			$cur_row_height = 0;
			foreach ($grid as $row => $column) {
				shuffle($column);
				$shuffledGrid[] = $column;
				$cur_col_width  = 0;
				foreach ($column as $col => $data) {

					imagecopyresampled($im2,$this->image,
						$data["x"],$data["y"],
						$cur_col_width,$cur_row_height,
						$data["width"],$data["height"],
						$data["width"],$data["height"]
					);

					$cur_col_width += $data["width"];
				}

				$cur_row_height += $this->block_h;
				

			}
			
			$this->shuffledGrid = $this->merge($shuffledGrid,$grid);

			//save
			switch($this->mime){
				case "image/jpeg":
					imagejpeg($im2, $this->prefix . $this->imgname);
					break;
				case "image/gif":
					imagegif($im2, $this->prefix . $this->imgname);
					break;
				case "image/png":
					imagepng($im2, $this->prefix . $this->imgname);
					break;
			}

			imagedestroy($im2);

		}

		private function merge($arr1,$arr2){
			foreach ($arr1 as $i => $v1) {
				foreach ($v1 as $i2 => $v2) {
					foreach ($v2 as $key => $value) {
						$arr2[$i][$i2]["new_".$key] = $value;
					}
				}
				
			}
			return $arr2;
		}


		public function unShuffle(){
			$unShuffled = array();
			foreach ($this->shuffledGrid as $key => $value) {
				foreach ($value as $index => $data) {
					$unShuffled[$data["row"]][$data["col"]] = array(
						"x" => $data["new_x"],
						"y" => $data["new_y"],
						"width" => $data["new_width"],
						"height" => $data["new_height"]
 					);
				}
			}
			?>
				<br /><br /><br />
				
				<style>
					div.container{
						width : <?= $this->image_w ?>px;
						height : <?= $this->image_h ?>px;
					}
					div.container span{
						display:block;
						float:left;
						background-image: url('<?= $this->prefix.$this->imgname ?>');
					}
					
					<?php foreach ($unShuffled as $row => $columns): ?>
						<?php foreach ($columns as $index => $data):  ?>

							#r<?= $row ?>c<?= $index ?>{
								width : <?= $data["width"] ?>px;
								height : <?= $data["height"] ?>px;
								background-position: -<?= $data["x"] ?>px -<?= $data["y"] ?>px;
							}
		
						<?php endforeach ?>
					<?php endforeach ?>
				</style>

				<div class="container">
					<?php foreach ($unShuffled as $row => $columns): ?>
						<?php foreach ($columns as $index => $data):  ?>
							<span id="r<?= $row ?>c<?= $index ?>"></span>
						<?php endforeach ?>
					<?php endforeach ?>
				</div>


			<?
		}

	}

	function gentime() {
	    static $a;
	    if($a == 0) $a = microtime(true);
	    else return (string)round((microtime(true)-$a),5);
	}

	$protected_image = new scramble("test.jpg");
	gentime();
	
	$protected_image->scramble();

	echo 'Generated in '.gentime().' seconds.';


	$protected_image->showShuffled();
	$protected_image->unShuffle();

?>
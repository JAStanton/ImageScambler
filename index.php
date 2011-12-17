<?php 
	/**
	 * Image Scramble Class
	 *
	 * This class will help prevent image stealing by
	 * scrambling the image and then using basic html /css
	 * to unscramble for the viewer, if the viewer
	 * decides to steal the image they will have to do a
	 * lot more work than they initially thought to steal
	 * or unscramble the image.
	 *
	 * @author Jonathan Stanton <jonathan@jastanton.com>
	 * @copyright Copyright (c) 2011, Jonathan Stanton
	 * @link http://www.jastanton.com Personal Website
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	  * @version 1.0 
	 */
	class scramble {
		
 		/**
    	* Width in pixels of each column that we divide the picture into
    	* @var block_w 
    	*/	
    	var $block_w = 100;

 		/**
    	* Height in pixels of each row that we divide the picture into
    	* @var block_h 
    	*/	
		var $block_h = 100;

 		/**
    	* prefix of the outputed scrambled image
    	* @var block_h 
    	*/	
		var $prefix = "shuffled_";


		/**
		 * Constructor loads the image 
		 *
		 * @param string $imgname
		 * @author Jonathan Stanton
		 */
		function __construct($imgname) {
			
			$this->imgname = $imgname;
			
			list($this->dirname, $this->basename, $this->extension, $this->filename) = array_values(pathinfo($imgname));

			$this->imaged_shuffled_path = $this->dirname . "/" . $this->prefix . $this->basename;

	
			$size = @getimagesize($this->imgname);
			$this->mime = $size["mime"];
						
			switch($this->mime){
				case "image/jpeg":
					$this->image = imagecreatefromjpeg($this->imgname); //jpeg file
					break;
				case "image/gif":
					$this->image = imagecreatefromgif($this->imgname); //gif file
					break;
				case "image/png":
					$this->image = imagecreatefrompng($this->imgname); //png file
					break;
				default: 
					echo "Error loading $imgname";
					die(); //fatal
			}
			
			list($this->image_w,$this->image_h) = $size;

			/* 
			 	I take the mod of the image divied by the width and the
			   	height to determine if our block sizes divide evenly. 
			*/
			$this->block_w_mod = $this->image_w % $this->block_w;
			$this->block_h_mod = $this->image_h % $this->block_h;
		}

		/**
		 * Displays the original image.
		 *
		 * @return void
		 * @author Jonathan Stanton
		 */
		public function showOriginal(){
			echo "<img src='".$this->imgname."' alt=''>";
		}
		
		/**
		 * Displays the shuffled image.
		 *
		 * @return void
		 * @author Jonathan Stanton
		 */
		public function showShuffled(){
			echo "<img src='".$this->imaged_shuffled_path."' alt=''>";
		}
		
		/**
		 * Scrambles and saves the image.
		 *
		 * @return void
		 * @author Jonathan Stanton
		 * @todo Save PNG with transparency
		 */
		public function scramble(){
			
			$cols = floor($this->image_w / $this->block_w);
			$rows = floor($this->image_h / $this->block_h);

			$grid = array();
			
			/* loop through each block and save the data in a matrix */
			for ($row=0; $row < $rows; $row++) { 
				for ($col=0; $col < $cols; $col++) { 

					// if our image doesn't divide evenly into
					// our block size, some blocks might have to be slightly
					// larger than others. 

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
			
			/* create a blank canvas to paint on */
			$im2 = ImageCreateTrueColor($this->image_w, $this->image_h);
			
			$shuffledGrid = array();
			$cur_row_height = 0;
			foreach ($grid as $row => $column) {
				
				shuffle($column); /* shuffle everything in the row */
				$shuffledGrid[] = $column;
				
				/* 
					I am beginning to stich the shuffled pieces together
				   	the idea behind this is to start each row
				    at the beginning (x) and paste the image, then start
					with the next block where I just left off (X + prev image width)
				*/
				$cur_col_width  = 0; 
				foreach ($column as $col => $img_data) {
					imagecopyresampled($im2,$this->image, /* dst_image, src_image */
						$img_data["x"],$img_data["y"], /* dst_x, dst_y */
						$cur_col_width,$cur_row_height, /* src_x, src_y */
						$img_data["width"],$img_data["height"], /* dst_w, dst_h */
						$img_data["width"],$img_data["height"] /* src_w, src_h */
					);
					$cur_col_width += $img_data["width"]; 
				}
				$cur_row_height += $this->block_h;
			}
			
			/* 
				combining the shuffled grid and the non shuffled 
			   	grid to get an idea of what I am working with.
			*/
			$this->shuffledGrid = $this->merge($shuffledGrid,$grid);

			//save the image
			switch($this->mime){
				case "image/jpeg":
					imagejpeg($im2, $this->imaged_shuffled_path,100); /* 100% jpeg quality*/
					break;
				case "image/gif":
					imagegif($im2, $this->imaged_shuffled_path);
					break;
				case "image/png":
					imagepng($im2, $this->imaged_shuffled_path);
					break;
			}
			imagedestroy($im2); // ♫ clean up clean up everybody do your share ♫!
		}
		
		/**
		 * Merge array combines two arrays
		 *
		 * @param array $arr1 
		 * @param array $arr2 
		 * @return void
		 * @author Jonathan Stanton
		 */
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

		/**
		 * unShuffle and output HTML
		 *
		 * @return void
		 * @author Jonathan Stanton
		 */
		public function unShuffle(){
			$unShuffled = array();
			foreach ($this->shuffledGrid as $row => $columns) {
				foreach ($columns as $index => $data) {
					$unShuffled[$data["row"]][$data["col"]] = array(
						"x" => $data["new_x"],
						"y" => $data["new_y"],
						"width" => $data["new_width"],
						"height" => $data["new_height"]
 					);
				}
			}
			?>
				<style>
					div.container{
						width: <?= $this->image_w ?>px;
						height: <?= $this->image_h ?>px;
					}
					div.container span{
						display:block;
						float:left;
						background-image: url('<?= $this->imaged_shuffled_path ?>');
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

	$protected_image = new scramble("bear.jpg"); /* instantiate */
	$protected_image->scramble();
	$protected_image->showShuffled();
	$protected_image->unShuffle();
	$protected_image->showOriginal();

?>
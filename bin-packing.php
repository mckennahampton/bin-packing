<?php
	
	/*define the starting point*/
	$total_items_volume = 0;
	/*define the array*/
	$itemsMaxDimensions = array();
	$masterItemsMDArray = array();
	foreach ($items as $item) {
		$sc_product_id = $item['product_id'];
		$sc_quantity = $item['product_quantity'];
		/*this is for the while loop*/
		$i = 1;
		$total_current_volume = 0;
		/*loop through the item details*/
		/*while loop N amount of times, N being the current items' quantity*/
		while($sc_quantity >= $i) {
			/*define the dimensions for the specific item*/
			$width = $item["width"];
			$height = $item["height"];
			$depth = $item["depth"];
			$weight = $item["weight"];
			$volume = $depth * $height * $width;
			$item = array(
				"depth" => $depth,
				"width" => $width,
				"height" => $height,
				"volume" => $volume
			);
			/*add item as an array to the master items array*/
			array_push($masterItemsMDArray, $item);
			/*add the dimensions into the master item dimensions array*/
			array_push($itemsMaxDimensions, $width, $depth, $height);
			/*define the volume*/
			$this_volume = $width * $height * $depth;
			/*add the volume of this item to the master item volume variable*/
			$total_items_volume = $total_items_volume + $this_volume;
			/*Add up the weight*/
			$total_items_weight = $total_items_weight + $weight;
			/*add to $i for the while loop*/
			$i++;
		}
	} #END $s as $r
	
	
	/*We will sort the items by volume from smallest to greatest.*/
	$volume = array();
	// Now obtain a list of columns
	foreach ($masterItemsMDArray as $key => $row) {
		$volume[$key] = $row['volume'];
	}
	/*Now, sort $masterItemsMDArray by the $volume variable we just created*/
	array_multisort($volume, SORT_DESC, $masterItemsMDArray);	
	
	/*define starting point*/
	$is_items_contained = false;
	/*define starting point*/
	$items_fully_contained = false;
	/*count the boxes*/
	$boxCount = count($boxes);
	$boxCountCurrent = 1;
	/*loop through each box, comparing the volume of the box(es) to the master item volume*/
	foreach ($boxes as $box) {
		/*check to make sure we haven't already figure it out*/
			/*define this box's dimensions*/
			$bd = array($box["inner_width"], $box["inner_length"], $box["inner_depth"]);
			//I sorted the maxes from greatest to least for the boxes and items.
			//Doing it this way means that the item will fit, regardless of orientation.
			/*define the maxes for the box*/
			$boxMax1 = findNthLargestInArray($bd, 1);
			$boxMax2 = findNthLargestInArray($bd, 2);
			$boxMax3 = findNthLargestInArray($bd, 3);
			/*define the maxes for the for all the items from the master item variable*/
			$itemMax1 = findNthLargestInArray($itemsMaxDimensions, 1);
			$itemMax2 = findNthLargestInArray($itemsMaxDimensions, 2);
			$itemMax3 = findNthLargestInArray($itemsMaxDimensions, 3);
			/*define the maxes for the for all the items from $masterItemsMDArray*/
			/*count the array*/
			$itemCount = count($masterItemsMDArray);
			$itemCountCurrent = 0;
			$fittedItems = 0;
			foreach ($masterItemsMDArray as $item) {
				/*define the item's dimensions*/
				$itemWidth = $item["width"];
				$itemHeight = $item["height"];
				$itemDepth = $item["depth"];
				$itemDimensions = array($itemWidth, $itemHeight, $itemDepth);
				
				if ($boxMax1 > findNthLargestInArray($itemDimensions, 1) && $boxMax2 > findNthLargestInArray($itemDimensions, 2) && $boxMax3 > findNthLargestInArray($itemDimensions, 3)) {
					/*this item fits, try the next one*/
					$items_fully_contained = true;
					$fittedItems++;
				} elseif ($boxMax1 < findNthLargestInArray($itemDimensions, 1) && $boxMax2 < findNthLargestInArray($itemDimensions, 2) && $boxMax3 < findNthLargestInArray($itemDimensions, 3) && $boxCountCurrent === $boxCount) {
					$itemTooBig = true;
					echo $boxMax1 . " < " . findNthLargestInArray($itemDimensions, 1) . "<br>";
					echo $boxMax2 . " < " . findNthLargestInArray($itemDimensions, 2) . "<br>";
					echo $boxMax3 . " < " . findNthLargestInArray($itemDimensions, 3) . "<br>";
					echo $box["reference"];
				}
				elseif ($boxMax1 < findNthLargestInArray($itemDimensions, 1) && $boxMax2 < findNthLargestInArray($itemDimensions, 2) && $boxMax3 < findNthLargestInArray($itemDimensions, 3) && $boxCountCurrent < $boxCount) {
					$keep_going_on_boxes = true;
				}
				if ($boxMax1 > findNthLargestInArray($itemDimensions, 1) && $boxMax2 > findNthLargestInArray($itemDimensions, 2) && $boxMax3 > findNthLargestInArray($itemDimensions, 3) && $fittedItems == $itemCount && !isset($itemTooBig)) {
					$theyFit = true;
					$keep_going_on_boxes = false;
				}
				$itemCountCurrent++;
			}
			
			
			/*set the box mulitplier*/
			/*This should be tested with your specific boxes and products.*/
			$multiplier = 0.75;
			
			
			
			$box_number = 1;
			$box_volume = $box["inner_width"] * $box["inner_length"] * $box["inner_depth"] * $multiplier;
			/*if this is the right box and multiplier*/
			if ($keep_going_on_boxes != true && $box_volume > $total_items_volume && $theyFit == true) {
				$this_is_the_right_multiplier = true;
			}
			elseif ($keep_going_on_boxes != true && $theyFit == true && $box_volume < $total_items_volume) {
				while ($box_volume < $total_items_volume) {
					//increase the multiplier by 1.
					//If we found our box(es), then the loop won't run again.
					//If we didn't, the multiplier will be great for the next loop
					$multiplier= $multiplier + 0.75;
					$box_number++;
					/*define the current box volume*/
					$box_volume = $box["inner_width"] * $box["inner_length"] * $box["inner_depth"] * $multiplier;
				}
				$this_is_the_right_multiplier = true;
			}
			if ($keep_going_on_boxes != true && !isset($stop_loop) && !isset($itemTooBig) && $items_fully_contained == true && $theyFit == true && $this_is_the_right_multiplier == true) {
				$box_type = $box["reference"];
				$is_items_contained = true;
				//echo $box_volume;
				$items_fully_contained = true;
				$stop_loop = true;
				/*define the price*/
				$shipping_price = ($box["us_price"] * $box_number);;
				$final_box_number = $box_number;
			}
			$boxCountCurrent++;
		}
	if ($itemTooBig == true) {
		echo "item too big";
		echo $item["option_name"];
	}
	

 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//End Bin Packing Algorithm
?>

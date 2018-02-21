# bin-packing
A simple algorithm to find the smallest box size necessary relative to an array of items, based on volume.

Tutorial:

Before you start, it is assumed that you already have multidimensional arrays of items and boxes you will be comparing, called $items and $boxes.

This algorithm loops through items stored in an array called $items, with each item in the array having the following attributes:
 - height
 - width
 - depth
 - weight
 - name
 
 It also loops through boxes stored in an array called $boxes, with each box on the array having the following attributes:
 - inner-width
 - inner-length
 - inner-depth
 - reference
 - us_price (optional)
 
The "us_price" attribute is used to set a price once the correct box is selected. If this is unnecessay to you, you can delete line 141
 
If the algorithm was successful, it returns the following values:
 - $box_type (the box refernece)
 - $box_number (the quantity of boxes necessary to contain the items)
 - $shipping_price
 
 If the algorithm was unsuccessful, it will echo "item too big", and follow with the name of the item.
 
 This algorithm loops through the items and sorts them from smallest to largest, and does the same for the boxes. It compares the dimensions of the items against the dimensions of the boxes one at a time, and as long as the item can be contained by the current box it is being compared against, it moves on to the next item. If it loops through the entire array of items sucessfully, then the current box is the one selected. If any item is too large to fit in the box, it breaks the loops and moves on to the next box.
 
 Once it has successfully selected a box, it then compares the master volume of the items against the volume of the selected box. Since there is no way to ensure there is no wasted space with the box is physically filled with the items, the box's volume it multiplied by the $multiplier on line 112. The default multiplier is 0.75, which will decrease the boxes volue to 75% of it's original volume. I encourage you to test this with differenct values between 0.6 and 1 (never above 1), as the amount of wasted space you expect in each box will vary widely depending on your boxes and items in use.

If all the items will individually fit in the selected box, but the total items' volume is greater than the current box's volume, the box's quantity is increaded by 1 and the volumes are compared again. This will continue until the items' volume is less than the box's volume.

Now, the algorithm has sucessfully selected a box that all items will fit in, and it has returned the relative data.

I hope this helps someone out there, please feel free to modify with any improvements.

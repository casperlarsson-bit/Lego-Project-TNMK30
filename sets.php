<?php include "navbar.html";?>
    <?php
		//Checking if a set has been chosen
		if (isset($_GET["setid"])) 
		{ 
			$setID  = $_GET["setid"]; 
		} 
		
		//Checking if the picture to the set has been found
		if (isset($_GET["picture"])) 
		{ 
			$picture = $_GET["picture"]; 
			//Getting big picture from bricklink database
			$filename = "https://img.bricklink.com/ItemImage/SN/0/$setID.png";
			print ("<img src='$filename' alt='Bild saknas' class='setpic'>");
		}
		
		
	
		
			//Connecting to the database
			$connection = mysqli_connect("mysql.itn.liu.se","lego","", "lego");
			
			//Checking if connection was possible
			if (!$connection) 
			{
				die('MySQL connection error');
			}
			
			//Checking if the order was set
			if (isset($_GET["order"])) 
			{ 
				$order  = $_GET["order"]; 
			}
			
			//Desfault order
			else
			{
				$order=namedesc;
			}
			
			//Adding an switchcase to select the order
			switch($order)
			{
					case"namedesc":
					$orderby = "parts.Partname asc";
					break;

					case"nameasc":
					$orderby = "parts.Partname desc";
					break;
			
					case"countdesc":
					$orderby = "inventory.Quantity desc";
					break;
			
					case"countasc":
					$orderby = "inventory.Quantity asc";
					break;
			}
			
			//Variable for where the database should search
			$datatable = "inventory , colors, parts, images, sets";
			
			//Mysql search for the set
			$result = mysqli_query($connection, "SELECT DISTINCT
				colors.Colorname, parts.Partname, images.has_gif, 
				images.ItemID, images.ColorID, inventory.Quantity, sets.Setname, sets.Year
			
				FROM ".$datatable." 
			
				WHERE sets.SetID = '$setID'
				AND inventory.SetID=sets.SetID
				AND parts.PartID=inventory.ItemID
				AND colors.ColorID=inventory.ColorID 
				AND inventory.ItemID = images.ItemID
				AND colors.ColorID = images.ColorID
				AND inventory.Extra = 'N'
				ORDER BY ".$orderby."");
				
			$top_info = true;
		
			//Writing out the set
			while ($row = mysqli_fetch_array($result)) 
			{ 
				
				
				//Setting variable
				$partname = $row['Partname'];
				$partid = $row['PartID'];
				$is_gif = $row['has_gif'];
				$colorid = $row['ColorID'];
				$colorname = $row['Colorname'];
				$itemid = $row['ItemID'];
				$quantity = $row['Quantity'];
				$setname = $row['Setname'];
				$year = $row['Year'];
				
				// Only to be shown once but uses info from database
				if ($top_info) {
					//Printing out what set you're viewing
					print ("<div class ='setlist'>");
					print ("<h2>Visar set $setname ($year)</h2>");
						
					//Adding an drop down menu to slect the order
					print ("<div class='dropdown'>");
					print ("<button class='dropbtn'>Sortera efter</button>");
					print ("<div class='dropdown-content'>");
					print ("<a href='?setid=$setID&picture=$picture&order=namedesc'>Namn A-Ö</a>");
					print ("<a href='?setid=$setID&picture=$picture&order=nameasc'>Namn Ö-A</a>");
					print ("<a href='?setid=$setID&picture=$picture&order=countdesc'>Antal fallande</a>");
					print ("<a href='?setid=$setID&picture=$picture&order=countasc'>Antal stigande</a>");
					print ("</div>");
					print ("</div>");
				

					print("<table>");
					print("<tr>");
						print("<th>Namn</th>");
						print("<th>Färg</th>");
						print("<th>ItemID</th>");
						print("<th>Antal</th>");
						print("<th>Bild</th>");
					print("</tr>");
					
					$top_info = false;
				}
				
				//Test if part has .gif or .jpg
				if($is_gif == 1)
				{
					$file = ".gif";
				}
				else 
				{
					$file = ".jpg";
				}
				
				//Variable for the picture
				$filename = "http://www.itn.liu.se/~stegu76/img.bricklink.com/P/$colorid/$itemid$file";
				
				//Printing out the row
				print ("<tr ><td>$partname</td>");
				print ("<td>$colorname</td>");
				print ("<td>$itemid</td>");
				print ("<td>$quantity</td>");
				print ("<td><img src='$filename' alt='Bild saknas'></td>");	
			}
			
			print("</table>");
			
			//Second search that only show extra parts
			$result2 = mysqli_query($connection, "SELECT DISTINCT
			colors.Colorname, parts.Partname, images.has_gif, 
			images.ItemID, images.ColorID, inventory.Quantity
			
			FROM ".$datatable." 
			
			WHERE sets.SetID = '$setID'
			AND inventory.SetID=sets.SetID
			AND parts.PartID=inventory.ItemID
			AND colors.ColorID=inventory.ColorID 
			AND inventory.ItemID = images.ItemID
			AND colors.ColorID = images.ColorID
			AND inventory.Extra = 'Y'
			ORDER BY ".$orderby."");
			
			//Checking if there were any extra parts
			if(mysqli_num_rows($result2) != 0)
			{
				//Printing out an h3 and table + header
				print("<h3>Extra bitar</h3>");
				print("<table>");
				print("<tr>");
				print("<th>Namn</th>");
				print("<th>Färg</th>");
				print("<th>ItemID</th>");
				print("<th>Antal</th>");
				print("<th>Bild</th>");
				print("</tr>");
				
				//Writing out all the extra parts
				while ($row = mysqli_fetch_array($result2)) 
				{ 
					//Setting variables
					$partname = $row['Partname'];
					$partid = $row['PartID'];
					$is_gif = $row['has_gif'];
					$colorid = $row['ColorID'];
					$colorname = $row['Colorname'];
					$itemid = $row['ItemID'];
					$quantity = $row['Quantity'];
					
					//Test if part has .gif or .jpg
					if($is_gif == 1)
					{
						$file = ".gif";
					}
					else 
					{
						$file = ".jpg";
					}
					
					//Variable for picture
					$filename = "http://www.itn.liu.se/~stegu76/img.bricklink.com/P/$colorid/$itemid$file";
					
					//printing out the row
					print ("<tr ><td>$partname</td>");
					print ("<td>$colorname</td>");
					print ("<td>$itemid</td>");
					print ("<td>$quantity</td>");
					print ("<td><img src='$filename' alt='Bild saknas'></td>");
				}
				
				print("</table>");
			}
		?>
		</div>
	</body>
</html>
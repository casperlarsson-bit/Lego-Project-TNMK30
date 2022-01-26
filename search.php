<?php include "navbar.html";?>
	<?php
		//Connecting to database
		$connection = mysqli_connect("mysql.itn.liu.se","lego","","lego");
		
		//Checking if connection was possible
		if (!$connection) 
		{
			die('MySQL connection error');
		}
		
		//Adding variabels for results per page
		$results_per_page = 10;

		//Checking if a search have been made (either by searching or copying an url with a search in it)
		if (isset($_GET["search"]))
		{
			//Checking what the search was and removing white spaces
			$search = $_GET['search'];
			$search = trim($search, " \t\n\r\0\x0B");
			
			//Printing out text and search bar
			print("<h1 class='head'>Sök efter setID eller setnamn!</h1>");
			print("<form method='get' name='search' id='search'><input class='searchbarsmall' type='text' placeholder='Sök efter set...' name='search'></form>");
			
			//Checking if something was searched for (not just white spaces)
			if($search != null)
			{
				//Getting page number if it was set
				if (isset($_GET["page"])) 
				{ 
					$page  = $_GET["page"]; 
				} 
				//Make page 1 to original page
				else 
				{ 
					$page = 1; 
				}; 
				
				//Setting what page the database will dispisplay and how many items per page
				$start_from = ($page-1) * $results_per_page;
		
				//Variable for where the database will search from
				$datatable = "inventory, sets, images";
				
				$for_pages = mysqli_query($connection, "SELECT DISTINCT 
				inventory.SetID, sets.Setname
				
				FROM inventory, sets 
				
				WHERE (sets.SetID LIKE '%$search%'
				OR sets.Setname LIKE '%$search%')
				AND inventory.SetID = sets.SetID");
				
				//MySql search for both set name and set id
				$result = mysqli_query($connection, "SELECT DISTINCT
				sets.SetID, sets.Setname, images.has_gif,
				images.ItemID 
			
				FROM ".$datatable." 
			
				WHERE sets.SetID = inventory.SetID 
				AND (sets.Setname LIKE '%$search%'
				OR sets.setID LIKE '%$search%')
				AND inventory.SetID = images.ItemID
			
				ORDER BY sets.Setname asc
			
				LIMIT $start_from, $results_per_page");
				
				$number_of_results = mysqli_num_rows($for_pages);

				//Checking if it was an result or not
				if(mysqli_num_rows($result) != 0)
				{
					//Printing out the table and headers
					print("<div class='infotext'><p>Visar $number_of_results resultat för $search</p></div>");
					print("<table class='searchtable'>");
					print("<tr>");
					print("<th>Namn</th>");
					print("<th>SetID</th>");
					print("<th>Bild</th>");
					print("<th>Mer Info</th>");
					print("</tr>");

					//Setting variables for page select
					//Flytta på
					$next = $page + 1;
					$prev = $page - 1;
					
					//Writing out the result from the database
					while ($row = mysqli_fetch_array($result)) 
					{ 
						//Setting variables
						$setid = $row['SetID'];
						$setname = $row['Setname'];
						$is_gif = $row['has_gif'];
						$is_big_gif = $row['has_largegif'];
						$is_big_jpg = $row['has_largejpg'];
						
						//Test if part has .gif or .jpg
						if($is_gif == 1)
						{
							$file = ".gif";
						}
						else 
						{
							$file = ".jpg";
						}
						
						//Adding variable for picture
						$filename = "http://www.itn.liu.se/~stegu76/img.bricklink.com/S/$setid$file";

						//Printing out the table and adding buttons to get to the specific set
						print ("<tr><td><a href='sets.php?setid=$setid&picture=$file'>$setname</a></td>");
						print ("<td>$setid</td>");
						print ("<td><img src='$filename' alt='Bild saknas'></td>");
						print ("<td><a href='sets.php?setid=$setid&picture=$file' class='buttonInfo'>Mer Info</a></td></tr>");
					}
					print ("</table>");
					
					print("<div class='page_select'>");
					
					//Adding an variable for all the items from the search
					$count = mysqli_num_rows($for_pages);
					
					//Checking if page if abow 1
					if($page > 1)
					{
						print ("<a href='?page=1&search=$search'>1</a>");
						print ("<a href='?page=$prev&search=$search'><i class='fas fa-angle-left'></i></a>");
					}
					
					//Displaying current page
					print (" Sida $page ");

					//Setting the total amount of pages
					$count = $count / $results_per_page;
					$count = intval($count +1);
					
					//Checking if the page is above the total amount of pages
					if($page < $count)
					{
						print ("<a href='?page=$next&search=$search'><i class='fas fa-angle-right'></i></a>");
						print ("<a href='?page=$count&search=$search'><i></i>$count</a>");
					}
					
					print("</div>");
				}
				
				//Adding an error message for an complicated search without results
				else
				{
					print("<h3 class='errormessage'>Din sökning gav inga resultat</h3>");
				}
			}
			
			//Adding an error message for just using blank spaces or doing an empty search
			else
			{	
				print("<h3 class='errormessage'>Du angav en felaktig sökning, prova att inte göra en tom sökning</h3>");
			}
			
		}	
		
		//Adding what to display if no search has been made
		else
		{
			print("<div class='center'>");
				print("<h1 class='searchbar'>Sök efter setID eller setnamn!</h1>");
				print("<form method='get' name='search' id='search'><input class='searchbar' type='text' placeholder='Sök efter set...' name='search' ></form>");	
			print("</div>");
		}
		?>
	</body>
</html>
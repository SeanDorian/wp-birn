<?php

/*
 Template Name: CP Test

function SQLResultTable($Query)
{
    $Table = "";  //initialize table variable
    
    $Table.= "<table border='1' style=\"border-collapse: collapse;\">"; //Open HTML Table
    
    $Result = mysql_query($Query); //Execute the query
        //Header Row with Field Names
        $NumFields = mysql_num_fields($Result);
        $Table.= "<tr style=\"background-color: #000066; color: #FFFFFF;\">";
        for ($i=0; $i < $NumFields; $i++)
        {     
            $Table.= "<th>" . mysql_field_name($Result, $i) . "</th>"; 
        }
        $Table.= "</tr>";
    
        //Loop thru results
        $RowCt = 0; //Row Counter
        while($Row = mysql_fetch_assoc($Result))
        {
            //Alternate colors for rows
            if($RowCt++ % 2 == 0) $Style = "background-color: #00CCCC;";
            else $Style = "background-color: #0099CC;";
            
            $Table.= "<tr style=\"$Style\">";
            //Loop thru each field
            foreach($Row as $field => $value)
            {
                $Table.= "<td>$value</td>";
            }
            $Table.= "</tr>";
        }
    $Table.= "</table>";
    
    return $Table;
}*/
get_header(); ?>

<div id="primary" >
	<div id="view-user">
		<?php
		$shows = $wpdb->get_results( 
			"
			SELECT title, genre 
			FROM shows
			WHERE active = 1
			"
		);
		foreach ($shows as $show){
			echo $show->title;
			echo $show->genre;
		}		
		?>
	</div>
</div>

<?php 
include (TEMPLATEPATH . '/cp-footer.php'); ?>

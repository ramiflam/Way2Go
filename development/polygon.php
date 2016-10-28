<?php

$userName=$_COOKIE["userName"];

$fileTimestamp = date('Ymd');
$file = fopen("../logs/polygon_" . $fileTimestamp . ".txt","a");


// checks if location is inside polygon (later to change it one input array for polygon instead of 2
function is_in_polygon($vertices_x, $vertices_y, $longitude_x, $latitude_y)
{
  $polygonCount = count($vertices_x);
  $i = $j = $c = $pointsCount = 0;
  for ($i = 0, $j = $polygonCount ; $i < $polygonCount; $j = $i++) {
    $pointsCount = $i;
    if( $pointsCount == $polygonCount )
      $pointsCount = 0;
    if ( (($vertices_y[$pointsCount]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
     ($longitude_x < ($vertices_x[$j] - $vertices_x[$pointsCount]) * ($latitude_y - $vertices_y[$pointsCount]) / ($vertices_y[$j] - $vertices_y[$pointsCount]) + $vertices_x[$pointsCount]) ) )
       $c = !$c;
  }
  return $c;
}

function pointComp($a, $b) {
   if ($a['lng'] == $b['lng'])  {
      return 0;
   }  else {
       return ($a['lng'] < $b['lng']) ? -1 : 1;
   }  
} // compare points by x (lng) value ascending order


// function to create counding polygon for set of locations (Convex hull/Monotone chain implementation)
function convexHull($points) {
	// Ensure point doesn't rotate the incorrect direction as we process the hull halves 
	$cross = function($o, $a, $b) {
		// return ($a[0] - $o[0]) * ($b[1] - $o[1]) - ($a[1] - $o[1]) * ($b[0] - $o[0]);
		return ($a['lng'] - $o['lng']) * ($b['lat'] - $o['lat']) - ($a['lat'] - $o['lat']) * ($b['lng'] - $o['lng'])
	};
		
 	$pointCount = count($points);
 	// sort($points);
 	usort ($points, 'pointComp');
	if ($pointCount > 1) {
		$n = $pointCount;
		$k = 0;
		$h = array();
 
		/* Build lower portion of hull */
		for ($i = 0; $i < $n; ++$i) {
			while ($k >= 2 && $cross($h[$k - 2], $h[$k - 1], $points[$i]) <= 0)
				$k--;
			$h[$k++] = $points[$i];
		}
 
		/* Build upper portion of hull */
		for ($i = $n - 2, $t = $k + 1; $i >= 0; $i--) {
			while ($k >= $t && $cross($h[$k - 2], $h[$k - 1], $points[$i]) <= 0)
				$k--;
			$h[$k++] = $points[$i];
		}
		/* Remove all vertices after k as they are inside of the hull */
		if ($k > 1) {
			/* If you don't require a self closing polygon, change $k below to $k-1 */
			$h = array_splice($h, 0, $k); 
		}
		return $h;
	} // if $pointCount > 1
	else if ($pointCount <= 1) {
		return $points;
	}
	else  {
		return null;
	}
} // convexHull


?>
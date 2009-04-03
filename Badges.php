<?php
/**
 *  Licensed under WTFPL - DoWhatTheFuckYouWant Public License
 *    - Jesús García Sáez 2009-     <blaxter@gmail.com>
 *    - Hugues Hiegel     2006-2008 <hugues@hiegel.fr>
 */

ini_set( 'display_errors', 1 );
error_reporting(E_ALL | E_STRICT );


// get the parameters and initial setup

$username_orig = $_GET['user'];

$username = gpc_addslashes( strtolower( $username_orig ) );
$type     = $_GET['type' ];
$style    = $_GET['style'];
$color    = $_GET['color'];

include("Config.php"); // default values will be setted if they aren't defined

clearstatcache();

mysql_connect( MYSQL_HOST, MYSQL_USER, MYSQL_PASS );
mysql_select_db( MYSQL_DB );


// fetch data for badget output

$res  = mysql_query("SELECT * FROM users WHERE username='$username'");
$data = mysql_fetch_assoc($res);

$exists_user   = mysql_num_rows( $res ) > 0;
$expired_cache = $data["lastupdate"] && ( $data["lastupdate"] + CACHE ) < time();
if( ! $exists_user || $expired_cache )
{
  $data = make_db_cache( $username );
}

// generate badget if needed

$output_badge  = CACHE_FOLDER."/".image_filename();
if ( ! $data )
{   // no data
    $badges_lines = get_lines_for_not_found( $username_orig );
    $output_badge = NULL;

    header("Content-Type: image/png");
    generate_badge( $badges_lines, "Letters", "Red", $output_badge );
}
else
{
    $badge_has_to_be_generated = badge_has_to_be_generated(
        $username, $type, $style, $color,
        $data["lastupdate"], $output_badge
      );
    if ( $badge_has_to_be_generated )
    {
         $badges_lines = generate_lines_for( $data, $type, $username_orig );

         generate_badge( $badges_lines, $style, $color, $output_badge );
         mark_as_generated( $username, $type, $style, $color, $output_badge );
     }
}


if ( is_file( $output_badge ) )
{
    touch_badge($username, $type, $style, $color);

    $base_url = substr( $_SERVER['PHP_SELF'], 0,
                        strrpos($_SERVER['PHP_SELF'], '/') );
    header('Location: '.$base_url.'/'.CACHE_SUBFOLDER.'/'.image_filename());
}

exit;

// v lines for badget ----------------------------------------------------------

function generate_lines_for( $data, $type, $username_orig )
{
    $badges_lines = array( new Text );
    $playcount    = $data['playcount'];
    $statsstart   = $data['statsstart'];

    $duration  =  $_SERVER['REQUEST_TIME'] - $statsstart;
    $months    = $duration / (60*60*24*30);
    $weeks     = $duration / (60*60*24*7);
    $days      = $duration / (60*60*24);
    $permonth  = floor($playcount / $months);
    $perweek   = floor($playcount / $weeks);
    $perday    = floor($playcount / $days);

    switch( $type )
    {
        case "PerDay":
            $badges_lines[0]->value = "$perday tracks per Day";
            $badges_lines[0]->angle = rand(2,13);
            break;
        case "PerWeek":
            $badges_lines[0]->value = "$perweek tracks per Week";
            $badges_lines[0]->angle = rand(2,13);
            break;
        case "PerMonth":
            $badges_lines[0]->value = "$permonth tracks per Month";
            $badges_lines[0]->angle = rand(2,13);
            break;
        case "PerDay2":
            $badges_lines[] = new Text;
            $badges_lines[0]->value = "$perday";
            $badges_lines[0]->angle = rand(2,13);
            $badges_lines[1]->value = "tracks per Day";
            $badges_lines[1]->angle = rand(2,13);
            break;
        case "PerWeek2":
            $badges_lines[] = new Text;
            $badges_lines[0]->value = "$perweek";
            $badges_lines[0]->angle = rand(2,13);
            $badges_lines[1]->value = "tracks per Week";
            $badges_lines[1]->angle = rand(2,13);
            break;
        case "PerMonth2":
            $badges_lines[] = new Text;
            $badges_lines[1]->value = "tracks per Month";
            $badges_lines[0]->angle = rand(2,13);
            $badges_lines[0]->value = "$permonth";
            $badges_lines[1]->angle = rand(2,13);
            break;
        case "Trueness":
            $badges_lines[0]->value = "is ";
            $badges_lines[0]->value .= ($permonth > TRUENESS ? "an" : "a");
            $badges_lines[0]->angle = rand(2,13);
            $badges_lines[] = new Text;
            $badges_lines[1]->value = ($permonth > TRUENESS ? "untrue" : "true");
            $badges_lines[1]->value .= " listener";
            $badges_lines[1]->angle = rand(2,13);
            if (strlen($username_orig." ".$badges_lines[0]->value) >= strlen($badges_lines[1]->value))
            {
                $badges_lines[1]->value = $badges_lines[0]->value." ".$badges_lines[1]->value;
                $badges_lines[0]->value = $username_orig;
            }
            else
            {
                $badges_lines[0]->value = $username_orig." ".$badges_lines[0]->value;
            }
            break;
        case "Trueness2":
            $badges_lines[] = new Text;
            $badges_lines[] = new Text;
            $badges_lines[0]->value = "$username_orig is ";
            $badges_lines[0]->value .= ($permonth > TRUENESS ? "an" : "a" ) ;
            $badges_lines[0]->angle = rand(2,13);
            $badges_lines[1]->value = ($permonth > TRUENESS ? "Untrue" : "True");
            $badges_lines[1]->angle = rand(2,13);
            $badges_lines[2]->value = "listener";
            $badges_lines[2]->angle = rand(2,13);
            break;
        case "Since":
            $badges_lines[0]->value = strftime("since %B %Y", $statsstart);
            $badges_lines[0]->angle = rand(2,13);
            break;
        case "Since2":
            $badges_lines[] = new Text;
            $badges_lines[] = new Text;
            $badges_lines[0]->value = "listening since";
            $badges_lines[0]->angle = rand(2,13);
            $badges_lines[1]->value = strftime("%B", $statsstart);
            $badges_lines[1]->angle = rand(2,13);
            $badges_lines[2]->value = strftime("%Y", $statsstart);
            $badges_lines[2]->angle = rand(2,13);
            break;
        case "Total":
            $badges_lines[0]->value = "$playcount tracks played";
            $badges_lines[0]->angle = rand(2,13);
            break;
        case "Total2":
            $badges_lines[0]->value = "$playcount";
            $badges_lines[0]->angle = rand(2,13);
            $badges_lines[] = new Text;
            $badges_lines[1]->value = "tracks played";
            $badges_lines[1]->angle = rand(2,13);
            break;
        default:
            $badges_lines[0]->value = "Sorry !";
            $badges_lines[0]->angle = rand(2,13);
            $badges_lines[] = new Text;
            $badges_lines[1]->value = "Not available";
            $badges_lines[1]->angle = rand(2,13);
            break;
    }

    return $badges_lines;
}

// v auxiliar functions --------------------------------------------------------

function mark_as_generated( $username, $type, $style, $color, $output_badge )
{
     $hits = badges_hits( $username, $type, $style, $color );
     $q = "REPLACE INTO badges (username, type, style, color, lastupdate, png, hits)
         VALUES ('$username','$type','$style','$color', ".time().", '$output_badge', $hits);"
     ;
     mysql_query($q);
}

function badge_has_to_be_generated(
    $username, $type, $style, $color, $lastupdate, $file )
{
    $exists_badge = is_file( $file ) && filesize( $file ) > 0;
    if ( ! $exists_badge )                  return True;

    $is_from_old_update = filemtime( $file ) < $lastupdate;
    if ( $is_from_old_update ) return True;

    // this is the same check that $is_from_old_update
    // but querying to the DB, maybe the files has been manually modified...
    $res = mysql_query(
        "SELECT lastupdate FROM badges
         WHERE  username='$username' AND
                type='$type'         AND
                style='$style'       AND
                color='$color'
        ;"
       );
    $badge = mysql_fetch_assoc($res);

    return $badge['lastupdate'] < $lastupdate;
}

function get_lines_for_not_found( $username )
{
    $badges_lines = array( new Text );
    $badges_lines[0]->value="Sorry, $username is not";
    $badges_lines[0]->angle=rand(-1,2);

    $badges_lines[] = new Text;
    $badges_lines[1]->value="a valid Last.fm account";
    $badges_lines[1]->angle=rand(-2,1);

    return $badges_lines;
}

function generate_badge( $badges_lines, $style, $color, $output_badge )
{
    global $Styles, $Colors;

    foreach ($badges_lines as $line)
    {
        $line->font = $Styles[$style];
    }

    $y=0;
    foreach ($badges_lines as $line)
    {
        $line->initiate( $line->size, $line->angle, $line->font, $line->value );
        $y += $line->height;
        $line->y = $y;
    }

    $Image = new Text;
    $Image->width   = WIDTH;
    $Image->height  = $y;

    $img = imagecreatetruecolor( $Image->width, $Image->height );
    imagealphablending( $img, FALSE );
    imagesavealpha( $img, TRUE );

    foreach ( $badges_lines as $line )
    {
        $line->color= imagecolorallocate(
            $img,
            get_color( "r", $Colors[$color] ),
            get_color( "g", $Colors[$color] ),
            get_color( "b", $Colors[$color] )
          );
    }

    $transparent = imagecolorallocatealpha( $img, 255, 255, 255, 127 );

    imagefilledrectangle( $img, 0, 0, $Image->width, $Image->height, $transparent );

    foreach ( $badges_lines as $line )
    {
        imagettftext( $img, $line->size, $line->angle, $line->x, $line->y, $line->color, $line->font, $line->value );
    }

    imagepng( $img, $output_badge );
    imagedestroy( $img );
}

function make_db_cache( $username ){
    $data = array( 'lastupdate' => time(),
                   'playcount'  => 0,
                   'statsstart' => 0,
                   'username'   => $username );
    $profile_xml = file_get_contents("http://ws.audioscrobbler.com/1.0/user/".rawurlencode($username)."/profile.xml");
    $feed = new XMLReader();

    if( $profile_xml && $feed->xml( $profile_xml ) )
    {
        while ( $feed->read() )
        {
            if ( $feed->nodeType == XMLReader::ELEMENT )
            {
                switch ( $feed->name )
                {
                    case "playcount":
                        $data['playcount'] = intval( $feed->readString() );
                        break;
                    case "registered":
                    case "statsreset":
                        $data['statsstart'] = $feed->getAttribute( "unixtime" );
                        break;
                    case "profile":
                       $data['username'] = $feed->getAttribute( "username" );
                       break;
                }
            }
        }

        if ( $data['playcount'] != 0 )
        {
            $q= sprintf(
                "REPLACE INTO users (lastupdate,playcount,statsstart,username)
                 VALUES ('%s',%s,'%s','%s');",
                 $data['lastupdate'], $data['playcount'],
                 $data['statsstart'], $username
              );
            mysql_query( $q );
        }
    }

    return ( $data['playcount'] != 0 ) ? $data : NULL;
}

function badges_hits( $username, $type, $style, $color )
{
    $res = mysql_query(
       "SELECT hits FROM badges
        WHERE  username='$username' AND
               type='$type'         AND
               style='$style'       AND
               color='$color';"
      );

    if( mysql_num_rows( $res ) )
    {
        $data = mysql_fetch_assoc($res);
        $hits = $data["hits"];
    }
    else
    {
        $hits = 0;
    }
    return $hits;
}

function touch_badge( $username, $type, $style, $color )
{
    $hits = badges_hits( $username, $type, $style, $color ) + 1;
    mysql_query(
        "UPDATE badges SET hits=$hits, lasthit='".time()."'
         WHERE username='$username' AND type='$type'   AND
               style='$style'       AND color='$color'     ;"
      );
}

function gpc_addslashes( $str )
{
  return ( get_magic_quotes_gpc() ? $str : addslashes( $str ) );
}

function get_color( $color, $code )
{
  switch($color)
  {
    case "r":
      return ($code >> 16) & 0xff;
      break;
    case "g":
      return ($code >>  8) & 0xff;
      break;
    case "b":
      return ($code >>  0) & 0xff;
      break;
  }
}

function image_filename()
{
  global $username, $type, $style, $color;
  return rawurlencode($username)."_$type-$style-$color.png";
}

// v auxiliar classes ----------------------------------------------------------

class Text
{
    var $width  = 0;
    var $height = 0;
    var $x      = 0;
    var $y      = 0;

    var $font  = "";
    var $size  = 150; // High values to better quality
    var $angle = 0;
    var $color = 0;
    var $value = "";

    function initiate( $line_size, $angle, $font, $value )
    {
        $size = imageftbbox( $line_size, $angle, $font, $value );
        $this->width = abs(
              max(    $size[0], $size[2], $size[4], $size[6] )
            - min( 0, $size[0], $size[2], $size[4], $size[6] )
          );
        $this->height= abs(
              max(    $size[1], $size[3], $size[5], $size[7] )
            - min( 0, $size[1], $size[3], $size[5], $size[7] )
          );

        $ratio = WIDTH / $this->width;
        $this->width  *= WIDTH;
        $this->height *= $ratio;
        $this->size   *= $ratio;
    }
}


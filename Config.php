<?

define( "TRUENESS", 4000,    true );
define( "CACHE",    3600*48, true );
define( "WIDTH",    160,     true );
define( "HEIGHT",   20,      true );

putenv( 'GDFONTPATH=' . realpath('./fonts/') );
define( "CACHE_SUBFOLDER", "cache", true );
define( "CACHE_FOLDER", realpath( './'.CACHE_SUBFOLDER ) );

include("Config.mysql.php");
 
$Styles = array (
     "Modern"   => "It_wasn_t_me",
     "Letters"  => "JackOLantern",
     "Elegant"  => "ITCEdScr",
     "Screamy"  => "Junkyard",
     "Girlie"   => "girlw___",
     "Funny"    => "PenguinAttack",
     "Curly"    => "Curlz___",
     "Ruritania"=> "Ruritania",
     "Simple"   => "Georgia",
     "Morpheus" => "Morpheus",
     "Flamy"    => "Baileysc",
     "FaceLift" => "facerg__",
     "TypeO"    => "typeo___",
     "Grindy"   => "jackthehipper",
     "Horrorful"=> "horrh___"
);

$Colors = array(
    "Black"     => 0x000000,
    "Red"       => 0xd11f3c,
    "Green"     => 0x32dc32,
    "Yellow"    => 0xdcdc32,
    "Blue"      => 0x3232dc,
    "LightBlue" => 0x6666aa,
    "Gray"      => 0xdcdcdc,
    "White"     => 0xffffff
);

$Types = array(
    "Total"     => "Total tracks_ #1",
    "Total2"    => "Total tracks_ #2",
    "PerDay"    => "Daily tracks_ #1",
    "PerDay2"   => "Daily tracks_ #2",
    "PerWeek"   => "Weekly tracks_ #1",
    "PerWeek2"  => "Weekly tracks_ #2",
    "PerMonth"  => "Monthly tracks_ #1",
    "PerMonth2" => "Monthly tracks_ #2",
    "Since"     => "Since #1",
    "Since2"    => "Since #2",
    "Trueness"  => "Trueness #1",
    "Trueness2" => "Trueness #2"
);

// DEFAULT VALUES
if ( !isset($user) || $user == "" )         $user  = "blaxter";
if ( !array_key_exists( $style, $Styles ) ) $style = "Girlie" ;
if ( !array_key_exists( $color, $Colors ) ) $color = "Red"    ;
if ( !array_key_exists( $type,  $Types  ) ) $type  = "PerWeek";


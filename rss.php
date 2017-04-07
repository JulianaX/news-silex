<?php
require_once 'vendor/autoload.php';
use App\Model;
date_default_timezone_set('Europe/Kiev');
$connectionParams = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'rss_news',
    'user' => 'root',
    'password' => '123',
    'charset'   => 'utf8mb4',
);
$config = new \Doctrine\DBAL\Configuration;
$db = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
$feed_urls = [];
$source_mapper = new Model\SourceMapper($db);
$sources = $source_mapper->getSources();
foreach ($sources as $source) {
    if ($source->isActive()) $feed_urls[] = $source->getRssFeedLink();
}
$feed = new SimplePie();
$feed->enable_cache(false);
$feed->set_feed_url($feed_urls);
$feed->init();
$items = $feed->get_items();
$news_mapper = new Model\NewsMapper($db);
foreach ($items as $item) {
    $news = new Model\NewsEntity([
        'title'       => $item->get_title(),
        'link'        => $item->get_link(),
        'description' => $item->get_description(),
        'source'      => $item->get_feed()->get_link(),
        'pub_date'    => $item->get_date("Y-m-d H:i:s"),
    ]);
    $news_mapper->save($news);
}
/*$feed->handle_content_type();

// Set our paging values
$start = (isset($_GET['start']) && !empty($_GET['start'])) ? $_GET['start'] : 0; // Where do we start?
$length = (isset($_GET['length']) && !empty($_GET['length'])) ? $_GET['length'] : 5; // How many per page?
$max = $feed->get_item_quantity(); // Where do we end?

// When we end our PHP block, we want to make sure our DOCTYPE is on the top line to make
// sure that the browser snaps into Standards Mode.

$items = $feed->get_items();//повертає масив новин

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8" />
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
    <title>RSS-reader</title>
</head>
<body>
<script src="js/jquery-3.2.0.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<!--<script src="js/jquery.bootpag.min.js" type="text/javascript"></script>-->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            // If we have an error, display it.
            if ($feed->error())
            {
                echo '<div class="sp_errors">' . "\r\n";
                echo '<p>' . htmlspecialchars($feed->error()) . "</p>\r\n";
                echo '</div>' . "\r\n";
            }
            ?>

            <div id="results">
                <?php if ($success):
                    // get_items() will accept values from above.
                    foreach($feed->get_items($start, $length) as $item):
                        echo '<div = class="box"><p><h4><a href="'.$item->get_permalink().'">'.$item->get_title().'</a></h4></p><p>'.$item->get_description().'</p><p>' .$item->get_date("Y-m-d H:i:s"). '</p><p>' .$feed->get_link(). '</p></div>';

                    endforeach;
                endif; ?>

            </div>

            <div class="pagination">
                <?php
                // Let's do our paging controls
                $next = (int) $start + (int) $length;
                $prev = (int) $start - (int) $length;

                // Create the NEXT link
                $nextlink = '<a href="?start=' . $next . '&length=' . $length . '">Next &raquo;</a>';
                if ($next > $max)
                {
                    $nextlink = 'Next &raquo;';
                }

                // Create the PREVIOUS link
                $prevlink = '<a href="?start=' . $prev . '&length=' . $length . '">&laquo; Previous</a>';
                if ($prev < 0 && (int) $start > 0)
                {
                    $prevlink = '<a href="?start=0&length=' . $length . '">&laquo; Previous</a>';
                }
                else if ($prev < 0)
                {
                    $prevlink = '&laquo; Previous';
                }

                // Normalize the numbering for humans
                $begin = (int) $start + 1;
                $end = ($next > $max) ? $max : $next;
                ?>

                <p><?php echo $begin; ?>&ndash;<?php echo $end; ?> out of <?php echo $max; ?> | <?php echo $prevlink; ?> | <?php echo $nextlink; ?> | <a href="<?php echo '?start=' . $start . '&length=5'; ?>">5</a>, <a href="<?php echo '?start=' . $start . '&length=10'; ?>">10</a>, or <a href="<?php echo '?start=' . $start . '&length=20'; ?>">20</a> news.</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>*/

<?
	require("class.php");
	$protected_image = new scramble("bear.jpg"); /* instantiate */
	$protected_image->scramble(); /* scramble */
?>
<html>
<head>
	<title>ImageScrambler, experiement by Jonathan Stanton</title>
</head>
<body>
	<h1>ImageScrambler</h1>
	<h1>Using this tool</h1>

<h2>Background: </h2>

<p>The purpose of this image scrambler was more of a personal challenge than anything. I had that client want to protect images any way possible and a coworker (Ben) and I worked on this idea on the side but unfortunately never got an opportunity to implement this strategy.</p>

<p>The reason why I'm proud of it was because Ben was a lot better at programming than myself and when we worked together we would challenge each other to write the most efficient code, a healthy competition that spurred on fast learning techniques. We were both laid off before I was able to complete my version of this application but I'm proud to announce that for once I was finally able to write a more efficient code. I think you would be proud of me Ben :) your move!</p>

<h2>Unique Challenges:</h2>

<p>This may seem fairly straight forward but a huge difficulty that I faced writing this program was finding a technique that would allow me to divide an image evenly so when I pieced it together it wouldn't leave any stich marks (black lines). Sounds easy? It's more difficult then what it may seem!</p>

<h2>Example:</h2>

<pre><code>$protected_image = new scramble("bear.jpg"); /* instantiate */
$protected_image-&gt;scramble(); /* actual algorithm, writes to disk */
$protected_image-&gt;showShuffled(); /* Displays the shuffled image */
$protected_image-&gt;unShuffle(); /* unShuffle and output HTML */
$protected_image-&gt;showOriginal(); /* Displays original image */
</code></pre>

<hr>
<h2>Scrambled Image:</h2>
<p>Go on, try to steal this image:</p>
<? $protected_image->unShuffle(); ?>

<hr>
<h2>This is the scrambled image:</h2>
<? $protected_image->showShuffled(); ?>

<hr>
<h2>This was the original:</h2>
<?= $protected_image->showOriginal(); ?>

<p>TaDa!</p>
<a href="https://github.com/JAStanton/ImageScambler"><img style="position: fixed; top: 0; right: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/e6bef7a091f5f3138b8cd40bc3e114258dd68ddf/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f7265645f6161303030302e706e67" alt="Fork me on GitHub"></a>
</body>
</html>

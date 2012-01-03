#Using this tool

Background: 
----------

The purpose of this image scrambler was more of a personal challenge than anything. I had that client want to protect images any way possible and a coworker (Ben) and I worked on this idea on the side but unfortunately never got an opportunity to implement this strategy.

The reason why I'm proud of it was because Ben was a lot better at programming than myself and when we worked together we would challenge each other to write the most efficient code, a healthy competition that spurred on fast learning techniques. We were both laid off before I was able to complete my version of this application but I'm proud to announce that for once I was finally able to write a more efficient code that I think you would be proud of Ben :) your move!

Unique Challenges:
----------------
This may seem fairly straight forward but a huge difficulty that I faced writing this program was finding a technique that would allow me to divide an image evenly so when I pieced it together it wouldn't leave any stich marks (black lines). Sounds easy? It's more difficult then what it may seem!


Example:
--------
> 	$protected_image = new scramble("bear.jpg"); /* instantiate */
	$protected_image->scramble(); /* actual algorithm, writes to disk */
	$protected_image->showShuffled(); /* Displays the shuffled image */
	$protected_image->unShuffle(); /* unShuffle and output HTML */
	$protected_image->showOriginal(); /* Displays original image */
<form role="search" method="get" class="search-form" action="<?=home_url( '/' )?>">
	<div>
		<label for="s">Search:</label>
		<input type="text" value="<?php echo isset( $_GET['s'] ) ? htmlentities( $_GET['s'] ) : ''; ?>" name="s" class="search-field" id="s" placeholder="Enter your search term here..." />
		<button type="submit" class="search-submit">Search</button>
	</div>
</form>

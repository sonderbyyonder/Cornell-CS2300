<!DOCTYPE html>

<html>

	<head>
		<meta charset="utf-8">
    	<link rel="stylesheet" type="text/css" href="../css/stylesheet.css">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<script src = "script/valid.js"></script>
		<title>Search Page</title>
	</head>

	<body>

		<!-- Pup info -->
		<?php include("pupInfo.php"); ?>

		<!-- Read pup info from data.txt -->
		<?php
			$allPups = array();

			// Add pup info from data.txt to pups array
			if (file_exists('data.txt')) {
				$dataFile = fopen('data.txt', 'r');
				$pupsArray = file('data.txt');

				foreach($pupsArray as $pup) {
					$line = str_replace('\n', '', $pup);
					$pupArray = explode( '\t', $line);
					$allPups[] = new Pup($pupArray[0], $pupArray[1], $pupArray[2], $pupArray[3], $pupArray[4], $pupArray[5], $pupArray[6]);
				}

				fclose($dataFile);
			}

			// Get emoji corresponding to personality
			function getEmoji($personality) {
				global $personalityOptions;

				return ("../assets/".$personalityOptions[$personality]);
			}
		?>

		<!-- Reset search to show all pups -->
		<?php
			if (isset($_POST['search-reset'])) {
				$pups = $allPups;
			}
		?>

		<!-- Search Functionality -->
		<?php 
			$searchMatchingTitle = "";
			$pups = $allPups;

			if (isset($_POST['search-submit'])) {

				// Open data.txt
				$dataFile = fopen("data.txt", "r"); 
				$pupsArray = file('data.txt');
				$pups = array();

				// Check if at least one of the categories are matched
				if (empty($_POST['searchAll']) && empty($_POST['searchName']) && empty($_POST['searchBreedSelect']) && empty($_POST['searchWeightSelect']) && empty($_POST['searchPersonalitySelect']) && empty($_POST['searchFavoriteToy']) && empty($_POST["searchSpecialTalent"])) {

                	$searchMatchingTitle = "Please fill in at least one field.";

        		} else {

        			// Get user input if available (Strip tags to prevent HTML injection & turn everything to lowercase so search is case insensitive)
        			$searchAll = isset($_POST['searchAll']) ? strip_tags(strtolower($_POST['searchAll'])) : false;
					$searchName = isset($_POST['searchName']) ? strip_tags(strtolower($_POST['searchName'])) : false;
					$searchBreed = isset($_POST['searchBreedSelect']) ? strip_tags(strtolower($_POST['searchBreedSelect'])) : false;
					$searchWeight = isset($_POST['searchWeightSelect']) ? strip_tags(strtolower($_POST['searchWeightSelect'])) : false;
					$searchPersonality = isset($_POST['searchPersonalitySelect']) ? strip_tags(strtolower($_POST['searchPersonalitySelect'])) : false;
					$searchFavoriteToy = isset($_POST['searchFavoriteToy']) ? strip_tags(strtolower($_POST['searchFavoriteToy'])) : false;
					$searchSpecialTalent = isset($_POST['searchSpecialTalent']) ? strip_tags(strtolower($_POST['searchSpecialTalent'])) : false;

					// Find matching in data.txt using search terms
					foreach ($pupsArray as $pup) {
						$searchEntry = strtolower($pup); 
						$line = str_replace('\n', '', $searchEntry);
						$lowercasePupArray = explode('\t', $line);

						$searchAllMatching = (empty($searchAll) || preg_match("/$searchAll/", $searchEntry));
						$nameMatching = (empty($searchName) || preg_match("/$searchName/", $lowercasePupArray[0]));
						$breedMatching = (empty($searchBreed) || (strcasecmp($searchBreed, $lowercasePupArray[1]) == 0));
						$weightMatching = (empty($searchWeight) || (strcasecmp($searchWeight, $lowercasePupArray[2]) == 0));
						$personalityMatching = (empty($searchPersonality) || (strcasecmp($searchPersonality, $lowercasePupArray[3]) == 0));
						$favoriteToyMatching = (empty($searchFavoriteToy) || preg_match("/$searchFavoriteToy/", $lowercasePupArray[4]));
						$specialTalentMatching = (empty($searchSpecialTalent) || preg_match("/$searchSpecialTalent/", $lowercasePupArray[5]));

						if ($searchAllMatching && $nameMatching && $breedMatching && $weightMatching && $personalityMatching && $favoriteToyMatching && $specialTalentMatching) {
							$originalLine = str_replace('\n', '', $pup);
							$pupArray = explode('\t', $originalLine);
							$pups[] = new Pup($pupArray[0], $pupArray[1], $pupArray[2], $pupArray[3], $pupArray[4], $pupArray[5], $pupArray[6]);
						}
					}

					// Display no matches message if no matches found
					if (count($pups) == 0) {
						$searchMatchingTitle = "No matches found.";
					} elseif (count($pups) == 1) {
						$searchMatchingTitle = "1 match found.";
					} else {
						$searchMatchingTitle = (count($pups) . " matches found.");
					}
        		}
			}
		?>

		<!-- Header -->
		<?php include("header.php"); ?>

		<!-- Navigation Bar -->
		<div id='navbar-items'>
			<h2 id='add-title' class=<?php echo getClass('../index.php'); ?>><a href='../index.php'><span>ADD</span></a></h2>
			<h2 id='search-title' class=<?php echo getClass('search.php'); ?>><a href='search.php'><span>SEARCH</span></a></h2>
		</div>

		<!-- Add and Search Area -->
		<div class="add-search-form">
			<div class="form-title-items">
				<h3 class="form-title">FIND PUPS!</h3>
				<h3 class="form-subtitle"><?php echo $searchMatchingTitle; ?></h3>
			</div>

			<!-- Search Form -->
			<form id="search-form" class="form" name="pupForm" action="search.php" method="POST">
				<div class="form-container">

					<!-- Search and name fields -->
					<div class="basic-profile-form">
						<div id="search-field">
							<img id="search-icon" src="../assets/search-icon.png" alt="search-icon">
							<input id="search-input-field" type="text" placeholder="SEARCH" name="searchAll"><br>
						</div>
						<input id="name-search-field" type="text" placeholder="NAME" name="searchName" maxlength="30" autofocus pattern="[A-Za-z-_ ]*" title="Letters, spaces, dashes, and underscores only."><br>
					</div>

					<!-- Breed, weight, and personality select boxes-->
					<div class="select-options">
						<select class="search-select" name="searchBreedSelect">
							<option selected="selected" value>BREED</option>
							<?php for ($i = 0; $i < count($breedOptions); $i++) { ?>
								<option value=<?php echo "{$i}" ?>><?php echo $breedOptions[$i] ?></option>
							<?php } ?>
						</select>
						<select class="search-select" name="searchWeightSelect">
							<option selected="selected" value>WEIGHT</option>
							<?php foreach($weightOptions as $weight) { ?>
								<option value=<?php echo "{$weight}" ?>><?php echo $weight ?></option>
							<?php } ?>
						</select>
						<select class="search-select" name="searchPersonalitySelect">
							<option selected="selected" value>PERSONALITY</option>
							<?php foreach($personalityOptions as $key=>$value) { ?>
								<option value=<?php echo "{$key}" ?>><?php echo $key ?></option>
							<?php } ?>	
						</select>
					</div>

					<!-- Favorite toy and special talent fields + submit and reset buttons -->
					<div class="specific-profile-form">
						<input id="favorite-toy-search-field" type="text" placeholder="FAVORITE TOY" name="searchFavoriteToy" maxlength="50"><br>
						<input id="special-talent-search-field" type="text" placeholder="SPECIAL TALENT" name="searchSpecialTalent" maxlength="50"><br>
						<input id="search-submit" class="form-button" type="submit" name="search-submit" value="Submit"> 
						<input id="search-reset" class="form-button" type="submit" name="search-reset" value="Reset"> 
					</div>

				</div>
			</form>
		</div>

		<!-- Puppy Catalog Section -->
		<div class="catalog">

			<!-- Puppy Catalog Title -->
			<div class="catalog-header">
				<h3 class="catalog-title">PUPS</h3>
			</div>

			<!-- Puppy Catalog -->
			<?php include("catalog.php"); ?>

		</div>

	</body>

</html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
</style>

<body>
<div class="customlabels column1">
<form action="" method="GET" id="add">
  <h3>All Species Labels</h3>
  <input autocomplete="off" size="18" type="text" placeholder="Search Species..." id="exclude_species_searchterm" name="exclude_species_searchterm">
  <br>
  <span>Once the desired species has been highlighted, click it and then click ADD to have it excluded.</span>
  <select name="species[]" id="species" multiple size="auto">
  <option>Choose a species below to add to the Excluded Species List</option>
  <?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    
    $filename = './scripts/labels.txt';
    $eachline = file($filename, FILE_IGNORE_NEW_LINES);
    
    foreach($eachline as $lines){echo 
  "<option value=\"".$lines."\">$lines</option>";}
  ?>
  </select>
  <input type="hidden" name="add" value="add">
</form>
<div class="customlabels smaller">
  <button type="submit" name="view" value="Excluded" form="add">>>ADD>></button>
</div>
</div>

<div class="customlabels column2">
  <table><td>
  <button type="submit" name="view" value="Excluded" form="add">>>ADD>></button>
  <br><br>
  <button type="submit" name="view" value="Excluded" form="del">REMOVE</button>
  </td></table>
</div>

<div class="customlabels column3">
<form action="" method="GET" id="del">
  <h3>Excluded Species List</h3>
  <br><br>
  <select name="species[]" id="value2" multiple size="auto">
<?php
  $filename = './scripts/exclude_species_list.txt';
  $eachline = file($filename, FILE_IGNORE_NEW_LINES);
  foreach($eachline as $lines){
    echo 
  "<option value=\"".$lines."\">$lines</option>";
}?>
  </select>
  <input type="hidden" name="del" value="del">
</form>
<div class="customlabels smaller">
  <button type="submit" name="view" value="Excluded" form="del">REMOVE</button>
</div>
</div>

<script>
    document.getElementById("add").addEventListener("submit", function(event) {
      var speciesSelect = document.getElementById("species");
      if (speciesSelect.selectedIndex < 1) {
        alert("Please click the species you want to add.");
        document.querySelector('.views').style.opacity = 1;
        event.preventDefault();
      }
    });

    var search_term = document.querySelector("input#exclude_species_searchterm");
    search_term.addEventListener("keydown", doSearch);
    //Index where we found a match
    var search_match_idx = 1;
    var last_search_term = "";

    function doSearch(eventObj) {
        //Don't do anything if the user is till composing
        if (eventObj.isComposing || eventObj.keyCode === 229) {
            return;
        }

        //If the key pressed is the enter key capture it, stop the form submitting and do the search
        if (eventObj.key === 'Enter' || eventObj.keyCode === 13) {
            eventObj.preventDefault();

            //User wants to submit the text as a search
            var search_text = search_term.value.toLowerCase();

            //Now look at the select list, loop over the options and try find part of the text in the option's name/text
            var species_select_list = document.querySelector('select#species');

            //if the search text differs from last time start the search from the beginning of te list
            if (search_text !== last_search_term) {
                //Also unselect the last match
                species_select_list[search_match_idx].removeAttribute('class');
                search_match_idx = 1;
            }

            //Start the loop at 1 so we skip the very initial value asking the user to select a option
            for (let i = search_match_idx; i < species_select_list.length; i++) {
                // if (species_select_list[i] !== 'undefined') {
                option_text = species_select_list[i].value;
                search_match_text = option_text.toLowerCase().includes(search_text)

                //Check if the item is already selected, that could mean that user may be searching the same phrase
                if (species_select_list[search_match_idx].getAttribute('class') === "exclude_species_list_option_highlight") {
                    species_select_list[search_match_idx].removeAttribute('class');
                    // species_select_list[search_match_idx].removeAttribute('style');
                    //Go to the next item
                    i++
                    continue;
                }

                //There was a match,
                if (search_match_text === true) {
                    //already on this item so skip it and continue with list
                    if (search_match_idx === i) {
                        i++
                        continue;
                    }
                    //Finally we havent found this item before
                    search_match_idx = i;

                    //Scroll into view and select it
                    species_select_list[search_match_idx].scrollIntoView({behavior: 'smooth', block: 'start'});
                    //Apply a style to the option since setting the selected value to true breaks scrolling on chrome :(
                    //the style is nicer anyway :)
                    species_select_list[search_match_idx].setAttribute('class', "exclude_species_list_option_highlight");
                    // species_select_list[search_match_idx].setAttribute('selected', 'true');

                    //break the loop
                    break;
                }
            }
            //Track what search term was used so we know when to start over
            last_search_term = search_text
        }
    }
</script>

</body>

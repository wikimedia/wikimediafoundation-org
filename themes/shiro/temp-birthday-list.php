
<section class="bg-light-blue historical-banner">
    <div class="Wikimedia-head">
      <div class="mw-980">
        <div class="Wikimedia-headitems">
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/image 14.png" alt="">
          <h1 class="font-Helvetica-Neue">My Wiki birthday</h1>
          <p class="font-georgia">Learn which historical figures and what interesting events you share a birthday with! </p>
        </div>
      </div>
    </div>
</section>

<section class="historic-birthday">
    <div class="Wikimedia-date">
      <div class="Wikimedia-under">
        <h4 class="font-Helvetica-Neue">When is your birthday?</h4>
        <a href="javascript:void(0)" class="font-georgia">Disclaimer*</a>
      </div>
      <div class="Wikimedia-dateitems">
        <form name="theForm" class="" id="birthday_form" action="<?php echo get_permalink(66065); ?>" method="POST">
          <div class="Wikimedia-dateselectitems">
            <select name="date" class="date_val">
                <?php 
				
				for($x = 1; $x <= 31; $x++){
					if(15 == $x){
						echo '<option value="'.$x.'" selected>'.$x.'</option>';
					}else{
						echo '<option value="'.$x.'">'.$x.'</option>';
					}
				}
				?>
              </select>
			</div>
          <div class="Wikimedia-monthselectitems">
            <select name="month" class="month_val">
                <?php 
					
					for($j = 1; $j <= 12; $j++){
						
						$dateObj   = DateTime::createFromFormat('!m', $j);
						$monthName = $dateObj->format('F');
						
						if(1 == $j){
							echo '<option value="'.$j.'" selected>'.$monthName.'</option>';
						}else{
							echo '<option value="'.$j.'">'.$monthName.'</option>';
						}
					}
				
				?>
              </select>
          </div> 
          <button type="submit" class="Wikimedia-searchbtn">Search</button>
        </form>
      </div>
    </div>
  </section>
  <section class="Wikimedia-itemsmain">
    <div class="Wikimedia-items">
      <p class="font-georgia">*My Wiki Birthday is a <a href="https://wikimediafoundation.org/">Wikimedia Foundation</a> creation launched to celebrate Wikipedia’s 21st birthday on January 14th, 2022. By inputting your day and month of birth, you can discover who shares your birthday (your birthday buddy), while also learning about other curious facts that took place on the same day. 
        <br><br>
        My Wiki Birthday was developed utilizing <a href="#">Wikidata</a>. The results with the most <a href="https://www.wikidata.org/wiki/Help:Sitelinks">sitelinks</a> appear first. The order in which results appear doesn't represent a preference of the Wikimedia Foundation or any favoritism whatsoever. It’s a mere reflection of the dataset.
        <br><br>
        Wikipedia is a free, collaborative encyclopedia written by hundreds of thousands of volunteers around the world. The Wikimedia Foundation, the nonprofit organization that hosts the platform, does not create or curate the contents on Wikipedia. Learn more in our <a href="https://www.wikidata.org/wiki/Help:Sitelinks">Frequently Asked Questions.</a> 
        <br><br>
        For more information, please read our <a href="https://wikimediafoundation.org/privacy-policy/">privacy policy.</a>
        
        </p>
      
    </div>
</section>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		/* This is required for AJAX to work on our page */
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		var date_val,month_val;
		
		/* Ajax Pagination */
		/* jQuery(document).on('click','.Wikimedia-searchbtn',function(e){
			e.preventDefault();
			date_val = jQuery('.date_val').val();
			month_val = jQuery('.month_val').val();
			console.log(date_val);
			
			load_birthday_list();
			//scrollToDiv();
			//jQuery('.pageli').removeClass('active');
		}); */
		/* === */	
		
		function load_birthday_list(){
					 			 
			date_val = jQuery('.date_val').val();
			month_val = jQuery('.month_val').val();
			
			var s_data = {				
				date_val: date_val,				
				month_val: month_val,				
				action: "get_birthday_data"			
			};
			
			jQuery.ajax({			  
				url : ajaxurl, 			  
				type : 'post',			  
				data : s_data,			  
				beforeSend: function() {				
					jQuery('.list_wrapper').hide();				
							  
				},			  
				error : function( response ) {				  
					console.log('Error retrieving the information: ' + response.status + ' ' + response.statusText);				  
					jQuery("body").removeClass('openloader');				  
					jQuery('.list_wrapper').show();				  
					jQuery('.list_wrapper').html(response);			  
				},			  
				success : function( response ) {				
					/* jQuery("body").removeClass('openloader');				
					jQuery('.list_wrapper').show();	*/
					jQuery('.result_data').html(response); 
					
					//console.log(response);
				}
			});		
		}
		
	});
	
	
</script>
{{-- Next Wizard --}}
<script type="text/javascript">
  $(document).ready(function () {
	  var navListItems = $('ul.setup-panel li a'),
			  allWells = $('.setup-content'),
			  allNextBtn = $('.nextBtn');
			  allPrevBtn = $('.prevBtn');

	  // allWells.hide();

		navListItems.click(function (e) {
			e.preventDefault();
			var $target = $($(this).attr('href')),
				$item = $(this);

			if (!$item.attr('disabled')) {
				navListItems.removeClass('btn-primary').addClass('btn-default');
				allWells.hide();
				$target.show();
				$target.find('input:eq(0)').focus();
			}
			else 
			{
				e.stopImmediatePropagation();
				return false;
			}
		});

		allNextBtn.click(function(){
			var curStep = $(this).closest(".setup-content"),
			  curStepBtn = curStep.attr("id"),
			  nowStepWizard = $('ul.setup-panel li a[href="#' + curStepBtn + '"]').parent(),
			  nextStepWizard = $('ul.setup-panel li a[href="#' + curStepBtn + '"]').parent().next().children("a"),
			  // prevStepWizard = $('ul.setup-panel li a[href="#' + curStepBtn + '"]').parent().prev().children("a"),
			  curInputs = curStep.find("input[type='text']");
			  isValid = true;

			$(".form-group").removeClass("has-error");
			for(var i=0; i<curInputs.length; i++){
				if (!curInputs[i].validity.valid){
					isValid = false;
					$(curInputs[i]).closest(".form-group").addClass("has-error");
				}
			}

			if (isValid)
			{
				nextStepWizard.removeAttr('disabled').trigger('click');
				nowStepWizard.removeClass('active').addClass('disabled');
				nowStepWizard.children().attr('disabled', 'disabled');
				nextStepWizard.parent().addClass('active').removeClass('disabled');
			}
		});

		allPrevBtn.click(function(){
		    var curStep = $(this).closest(".setup-content"),
		  	  curStepBtn = curStep.attr("id"),
		  	  nowStepWizard = $('ul.setup-panel li a[href="#' + curStepBtn + '"]').parent(),
		  	  prevStepWizard = $('ul.setup-panel li a[href="#' + curStepBtn + '"]').parent().prev().children("a"),
		  	  isValid = true;

		  	if (isValid)
		  	{
		  		prevStepWizard.removeAttr('disabled').trigger('click');
				prevStepWizard.parent().addClass('active').removeClass('disabled');
			  	nowStepWizard.children().attr('disabled', 'disabled');
			  	nowStepWizard.removeClass('active').addClass('disabled');
			}
		});

		$('ul.setup-panel li a').trigger('click');
	});
  </script>
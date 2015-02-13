if (!RedactorPlugins)
	var RedactorPlugins = {};

RedactorPlugins.viewTextarea = function() {
	return {
		init : function() {
			var button = this.button.add('view-textarea', 'Показать формулы');
			this.button.setAwesome('view-textarea', 'fa fa-superscript');
			this.button.addCallback(button, this.viewTextarea.previewToggle);
		},
		previewToggle : function() {
			//console.log(this);
			//console.log(this.$box[0].children[1]);
			/*
			if(this.$element.hasClass('foreword-redactor')) {
				var object = $('.'+forewordPreviewContainer);
			}
			
			if(this.$element.hasClass('questionField')) {
				var questionNumber = this.$editor.attr('data-question-number');
				var object = $('.'+eval('questionPreviewContainer_'.concat(questionNumber)));
			}
			
			object.toggle();
			*/
			var hideContainerStyles = {
				"visibility": "hidden",
				"position": "absolute"
			};
			
			var showContainerStyles = {
				"visibility": "",
				"position": ""
			};
			
			if(this.$editor.hasClass('redactor-editor-foreword')) {
				var previewObject = $('.'+forewordPreviewContainer);
				
				if ( forewordRedactorDetach ) {
					this.button.setInactive('view-textarea', 'bold');
					previewObject.css(hideContainerStyles);
					this.$toolbar.after(forewordRedactorDetach);
					forewordRedactorDetach = null;
				} else {
					this.button.setActive('view-textarea', 'bold');
					previewObject.css(showContainerStyles);
					
					forewordRedactorDetach = this.$editor.detach();
					this.$toolbar.after(previewObject);
				}
			}
			
			if(this.$editor.hasClass('redactor-editor-question')) {
				var questionNumber = this.$editor.attr('data-question-number');
				var previewObject = $('.'+eval('questionPreviewContainer_'.concat(questionNumber)));
				
				if ( eval('questionRedactorDetach_'.concat(questionNumber)) ) {
					this.button.setInactive('view-textarea', 'bold');
					previewObject.css(hideContainerStyles);
					this.$toolbar.after(eval('questionRedactorDetach_'.concat(questionNumber)));
					eval('questionRedactorDetach_'.concat(questionNumber, '=null;'));
				} else {
					this.button.setActive('view-textarea', 'bold');
					previewObject.css(showContainerStyles);
					eval('questionRedactorDetach_'.concat(questionNumber, '=this.$editor.detach();'));
					this.$toolbar.after(previewObject);
				}
			}
			
		}
	};
};
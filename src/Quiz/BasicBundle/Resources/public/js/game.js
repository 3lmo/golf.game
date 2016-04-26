var quiz_game = {
		
	settings: { 
		view: "", 
		loaded: 0, 
		current: -1, 
		answer: { 
			id: -1, 
			name: "" 
		}, 
		stats: { 
			game: [],
			correct: 0,
			wrong: 0
		} 
	},
	
	init: function (data) {
		$.extend(this.settings, data);
		this.settings.stats.type = data.type;
		switch (this.settings.type) {
		case "easy":
			this.buildEasy();
			break;
		case "medium":
			this.buildMedium();
			break;
		case "hard":
			this.buildHard();
			break;
		}
	},

	buildEasy: function () {	
		var self = this;
		var elements = [];
		$.each(self.settings.question, function (i, v) {
			elements[i] = $("<div id='game_element'></div>");
			elements[i].addClass("question" + i);
			$.each(v.order, function (ind, val) {
				var option = $('<div class="radio">\
								  <label>\
								    <input type="radio" name="question' + i + '" value="' + val.id + '">\
								    ' + val.name + '\
								  </label>\
								</div>');
				elements[i].append(option);
			});
			var button = '<button class="btn btn-lg btn-primary btn-block" id="submit" type="submit">Next</button>';
			elements[i].append(button);
			var image = new Image();
			image.src = "data:image/jpeg;base64, " + v.image;
			image.onload = function () {
				var img = $("<a href='#'><img src='data:image/jpeg;base64, " + v.image + "'></a>");
				elements[i].prepend(img);
				$("#game").append(elements[i]);
				self.settings.loaded++;
				if (self.settings.loaded == 10) {
					self.nextElement("#loading");
				}				
			}
		});
	},
	
	buildMedium: function () {
		var self = this;
		var elements = [];
		$.each(self.settings.question, function (i, v) {
			elements[i] = $("<div id='game_element'></div>");
			elements[i].addClass("question" + i);
			var option = $('<div class="radio">\
								<p>' + v.salutation + '</p>\
								<input class="form-control" placeholder="Last Name" required="" type="text" id="last_name" name="question' + i + '" />\
							</div>');
			elements[i].append(option);
			var button = '<button class="btn btn-lg btn-primary btn-block" id="submit" type="submit">Next</button>';
			elements[i].append(button);
			var image = new Image();
			image.src = "data:image/jpeg;base64, " + v.image;
			image.onload = function () {
				var img = $("<a href='#'><img src='data:image/jpeg;base64, " + v.image + "'></a>");
				elements[i].prepend(img);
				$("#game").append(elements[i]);
				self.settings.loaded++;
				if (self.settings.loaded == 10) {
					self.nextElement("#loading");
				}				
			}
		});
	},
	
	buildHard: function () {
		var self = this;
		var elements = [];
		$.each(self.settings.question, function (i, v) {
			elements[i] = $("<div id='game_element'></div>");
			elements[i].addClass("question" + i);
			var option = $('<div class="radio">\
								<p>' + v.salutation + '</p>\
								<input class="form-control" placeholder="First Name" required="" type="text" id="first_name" name="question' + i + '" />\
								<input class="form-control" placeholder="Last Name" required="" type="text" id="last_name" name="question' + i + '" />\
							</div>');
			elements[i].append(option);
			var button = '<button class="btn btn-lg btn-primary btn-block" id="submit" type="submit">Next</button>';
			elements[i].append(button);
			var image = new Image();
			image.src = "data:image/jpeg;base64, " + v.image;
			image.onload = function () {
				var img = $("<a href='#'><img src='data:image/jpeg;base64, " + v.image + "'></a>");
				elements[i].prepend(img);
				$("#game").append(elements[i]);
				self.settings.loaded++;
				if (self.settings.loaded == 10) {
					self.nextElement("#loading");
				}				
			}
		});
	},
	
	nextElement: function (selector) {
		var self = this;
		$(selector).parent().animate({
			left: -220
		}, 500, function () {			
			self.settings.current++;
			$(selector).css({
				display: "none"
			});
			$(selector).parent().css({
				left: 0
			});
			$(selector).remove();
			if (self.settings.current == 10) {
				$("#wrapper").append('<div class="newGame"><a href="' + homeUrl + '" class="btn btn-primary btn-lg" id="button">New Game</a></div>')
				$("#wrapper").css("width", "620px");
			}
			else {
				$(".question" + self.settings.current).find("button").bind("click" , function () {
					switch (self.settings.type) {
					case "easy":
						self.submitEasy(this);
						break;
					case "medium":
						self.submitMedium(this);
						break;
					case "hard":
						self.submitHard(this);
						break;
					}
					
				});
			}
			self.settings.lock = false;	
		});
	},
	
	submitEasy: function () {
		var self = this;
		if ($('input[name=question' + self.settings.current + ']:checked').length > 0 && !self.settings.lock) {
			self.settings.lock = true;
			self.settings.answer.id = parseInt($('input[name=question' + self.settings.current + ']:checked').val());
			self.settings.answer.name = $('input[name=question' + self.settings.current + ']:checked').parent().text().trim();
			//write stats
			if (self.settings.answer.id == self.settings.question[self.settings.current].correct.id) {
				var correct = {};
				correct.id = self.settings.question[self.settings.current].correct.id;
				correct.name = self.settings.question[self.settings.current].correct.name;
				correct.answer = self.settings.answer.name;
				correct.correct = true;
				self.settings.stats.game.push(correct);
				self.blinkCorrect();
			}
			else {
				var wrong = {};
				wrong.answer = self.settings.answer.name;
				wrong.correct = false;
				wrong.id = self.settings.question[self.settings.current].correct.id;
				wrong.name = self.settings.question[self.settings.current].correct.name;
				self.settings.stats.game.push(wrong);
				self.blinkWrong();
				self.blinkCorrect();
			}
		}		
	},
	
	submitMedium: function () {
		var self = this;
		if ($('input[name=question' + self.settings.current + ']').val() != "" && !self.settings.lock) {
			self.settings.lock = true;
			self.settings.answer.name = $('input[name=question' + self.settings.current + ']').val();			
			//write stats
			if (self.settings.answer.name.toLowerCase().trim() == self.settings.question[self.settings.current].correct.name.toLowerCase().trim()) {
				var correct = {};
				correct.id = self.settings.question[self.settings.current].correct.id;
				correct.name = self.settings.question[self.settings.current].correct.name;
				correct.salutation = self.settings.question[self.settings.current].salutation;
				correct.answer = self.settings.answer.name;
				correct.correct = true;
				self.settings.stats.game.push(correct);
				self.blinkCorrect();
			}
			else {
				var wrong = {};
				wrong.answer = self.settings.answer.name;
				wrong.correct = false;
				wrong.name = self.settings.question[self.settings.current].correct.name;
				wrong.salutation = self.settings.question[self.settings.current].salutation;
				self.settings.stats.game.push(wrong);
				self.blinkWrong();
			}
		}		
	},
	
	submitHard: function () {
		var self = this;
		if ($('input#first_name').val() != "" && $('input#last_name').val() != "" && !self.settings.lock) {
			self.settings.lock = true;
			self.settings.answer.name = $('input#first_name').val() + " " + $('input#last_name').val();
			//write stats
			if ($('input#first_name').val().toLowerCase().trim() == self.settings.question[self.settings.current].correct.first_name.toLowerCase().trim() && $('input#last_name').val().toLowerCase().trim() == self.settings.question[self.settings.current].correct.last_name.toLowerCase().trim()) {
				var correct = {};
				correct.id = self.settings.question[self.settings.current].correct.id;
				correct.name = self.settings.question[self.settings.current].correct.name;
				correct.salutation = self.settings.question[self.settings.current].salutation;
				correct.answer = self.settings.answer.name;
				correct.correct = true;
				self.settings.stats.game.push(correct);
				self.blinkCorrect();
			}
			else {
				var wrong = {};
				wrong.answer = self.settings.answer.name;
				wrong.correct = false;
				wrong.name = self.settings.question[self.settings.current].correct.name;
				wrong.salutation = self.settings.question[self.settings.current].salutation;
				self.settings.stats.game.push(wrong);
				self.blinkWrong();
			}
		}		
	},
	
	continueGame: function () {
		var self = this;
		if (this.settings.current < 9) {
			this.nextElement(".question" + this.settings.current);
		}
		else {
			$.each(self.settings.stats.game, function (i, v) {
				if (v.correct) {
					self.settings.stats.correct++;
				}
				else {
					self.settings.stats.wrong++;
				}
			});
			//finish game
			if (user_type == "anonymousAccount") {
				this.showResults();
			}
			else {				
				this.endGame();
			}						
		}
	},
	
	blinkCorrect: function () {
		var self = this;
		switch(this.settings.type) {
		case "easy":
			$('input[name=question' + self.settings.current + ']').each( function (){
				if ($(this).val() == self.settings.question[self.settings.current].correct.id) {
					$(this).parent().parent().animate({
						backgroundColor: "green",
						color: "white"
					}, 750, function () {self.continueGame();});
				}			
			});
			break;
		case "medium":			
			$('input[name=question' + self.settings.current + ']').parent().animate({
				backgroundColor: "green",
				color: "white"
			}, 750, function () {
				$(this).animate({
					backgroundColor: "#eee",
					color: "black"
				}, 750, function () {self.continueGame();});
			});		
			break;
		case "hard":			
			$('input[name=question' + self.settings.current + ']').parent().animate({
				backgroundColor: "green",
				color: "white"
			}, 750, function () {
				$(this).animate({
					backgroundColor: "#eee",
					color: "black"
				}, 750, function () {self.continueGame();});
			});		
			break;
		}
		
	},
	
	blinkWrong: function () {
		var self = this;
		switch(this.settings.type) {
		case "easy":
			$('input[name=question' + self.settings.current + ']').each( function (){
				if ($(this).val() == self.settings.answer.id) {
					$(this).parent().parent().animate({
						backgroundColor: "red",
						color: "white"
					}, 750, function () {
						$(this).animate({
							backgroundColor: "#eee",
							color: "black"
						}, 750);
					});
				}			
			});
			break;
		case "medium":
			$('input[name=question' + self.settings.current + ']').parent().animate({
				backgroundColor: "red",
				color: "white"
			}, 750, function () {
				$(this).animate({
					backgroundColor: "#eee",
					color: "black"
				}, 750, function () {self.continueGame();});
			});
			break;
		case "hard":
			$('input[name=question' + self.settings.current + ']').parent().animate({
				backgroundColor: "red",
				color: "white"
			}, 750, function () {
				$(this).animate({
					backgroundColor: "#eee",
					color: "black"
				}, 750, function () {self.continueGame();});
			});
			break;
		}
	},
	
	endGame: function () {
		var self = this;		
		$.ajax({
	        url: window.endGameUrl,
	        type: 'post',
	        dataType: 'json',
	        data: { stats: JSON.stringify(self.settings.stats) },
	        success: function(data) {
	        	self.showResults();
	        }
	    });
	},
	
	showResults: function () {
		var self = this;
		$.each(this.settings.stats.game, function (i, v) {
			var row = $("<div class='row'></div>");			
			row.append('<div class="col-xs-1 col-md-1">' + (i+1) + '</div>');
			row.append('<div class="col-xs-4 col-md-2">' + v.salutation + ' ' + v.answer + '</div>');
			row.append('<div class="col-xs-4 col-md-2">' + v.salutation + ' ' + v.name + '</div>');
			if (v.correct) {
				row.append('<div class="col-xs-1 col-md-1"><div id="correct_count"></div></div>');
				row.append('<div class="col-xs-1 col-md-1"></div>');
			}
			else {
				row.append('<div class="col-xs-1 col-md-1"></div>');
				row.append('<div class="col-xs-1 col-md-1"><div id="wrong_count"></div></div>');
			}
			$(".correct_count").text(self.settings.stats.correct);
			$(".wrong_count").text(self.settings.stats.wrong);
			$("#stats").append(row);
		});
		
		$("#game").append($("#stats-wrapper").html());
		this.nextElement(".question" + self.settings.current);
	}
}

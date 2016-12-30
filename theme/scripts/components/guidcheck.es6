var Component = require('./component')
var $ = window.jQuery

class Guidcheck extends Component {

	constructor(element, data) {
		super(element, data)


		this.guidsArray = $.map(data.guids, function(value, index) {
			return [value];
		});

	}

	get listeners() {
		return {
			'keyup #songedit-guid': 'handleChange',
			'keyup #songedit-title': 'handleChange'
		}
	}

	handleChange(e, self) {

        self.target = $('#songedit-guid')
        self.value = self.target.val()

		if (self.checkIfExist(self.value, self.guidsArray)) {

			self.target.addClass('exist')

			if(!self.target.is(':focus')) {

				for (var i = 2; i < 100; i++) {

                    self.newValue = self.target.val() + '-' + i

					if (!self.checkIfExist(self.newValue, self.guidsArray)) {
                        break
					}
				}

				self.target.val(self.newValue)
				self.target.removeClass('exist')

			}
		} else {
			self.target.removeClass('exist')
		}
	}

	checkIfExist(value, guids) {
		if (jQuery.inArray(value, guids) !== -1) {
			return true
		} else {
			return false
		}
	}
}


module.exports = Guidcheck


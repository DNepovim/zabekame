var Component = require('./component')
var $ = window.jQuery

class Search extends Component {

	constructor(element, data) {
		super(element, data)

	}

	get listeners() {
		return {
			'keyup #search-input': 'handleChange'
		}
	}

	handleChange(e, self) {

        $(self.data.target).show()

        self.value = $(self.data.input).val()

        self.targets = $(self.data.container).find(self.data.target)

        self.targets.each(function (i, val) {

            self.match = $(val).find(self.data.value).text().toLowerCase().search(self.value.toLowerCase())

            if (self.match == -1) {
                $(val).hide()
            }


        })

    }

}

module.exports = Search


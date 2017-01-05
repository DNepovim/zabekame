var Component = require('./component')
var $ = window.jQuery

class Songindex extends Component {

	constructor(element, data) {
		super(element, data)

	}

	get listeners() {
		return {
			'mouseenter .song-item': 'handleMouseover',
			'mouseout .song-item': 'handleMouseout'
		}
	}

	handleMouseover(e, self) {
        $(e.target).prev().addClass('hover')
        $(e.target).next().addClass('hover')
    }

	handleMouseout(e, self) {
		$(e.target).prev().removeClass('hover')
		$(e.target).next().removeClass('hover')
	}

}

module.exports = Songindex


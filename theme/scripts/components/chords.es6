var Component = require('./component')
var $ = window.jQuery

class Chords extends Component {

	constructor(element, data) {
		super(element, data)

		this.$lines = $('.chord-line')
		let lastChordOffsetRight
		let offsetCurrent
		$.each(this.$lines, function(i, v) {
			this.$chords = $(v).find('.chord')
			$.each(this.$chords, function(i, v) {
				let $v = $(v)
				let currentOffset = $v.offset().left
				if (i > 0) {
					if (currentOffset < lastChordOffsetRight) {
						let setOffset
						setOffset = lastChordOffsetRight - currentOffset + 10
						$v.css('left', setOffset)
					}

				}
				lastChordOffsetRight = $v.offset().left + $v.find('span').width()
			})
		})
	}
}


module.exports = Chords
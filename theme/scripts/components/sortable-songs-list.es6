const Component = require('./component')
const $ = window.jQuery

module.exports = class SortableSongList extends Component {

	constructor(element, data) {
		super(element, data)

		this.$el.sortable()
	}
}

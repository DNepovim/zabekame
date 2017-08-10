const Component = require('./component')
const $ = window.jQuery

module.exports = class SortableSongList extends Component {


	constructor(element, data) {
		super(element, data)

		this.$el.sortable({
			stop: this.handleSorted
		})
	}

	handleSorted() {

		const items = $(this).children()
		let list = {};

		$.each(items, function(i, v) {
			let $item = $(v)
			list[$item.data('id')] = $item.index()
		})

		const json = JSON.stringify(list)
		$('#song-order-input').val(json)

	}
}

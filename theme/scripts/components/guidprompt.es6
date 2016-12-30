var Component = require('./component')
var $ = window.jQuery

class Guidprompt extends Component {

	constructor(element, data) {
		super(element, data)
	}

	get listeners() {
		return {
			'keyup #songedit-title': 'handleChange'
		}
	}

	handleChange(e, self) {
        self.value = $(e.target).val()
        self.value = self.removeDiacritics(self.value)
		$(self.data.guid).val(self.value)
	}

	removeDiacritics(s){
		var r=s.toLowerCase();
		r = r.replace(new RegExp("\\s", 'g'),"-");
		r = r.replace(new RegExp("[àáâãäå]", 'g'),"a");
		r = r.replace(new RegExp("æ", 'g'),"ae");
		r = r.replace(new RegExp("ç", 'g'),"c");
		r = r.replace(new RegExp("[èéêëeěé]", 'g'),"e");
		r = r.replace(new RegExp("[ìíîï]", 'g'),"i");
		r = r.replace(new RegExp("ñ", 'g'),"n");
		r = r.replace(new RegExp("[òóôõö]", 'g'),"o");
		r = r.replace(new RegExp("œ", 'g'),"oe");
		r = r.replace(new RegExp("[ùúûü]", 'g'),"u");
		r = r.replace(new RegExp("[č]", 'g'),"c");
		r = r.replace(new RegExp("[š]", 'g'),"s");
		r = r.replace(new RegExp("[ř]", 'g'),"r");
		r = r.replace(new RegExp("[ž]", 'g'),"z");
		r = r.replace(new RegExp("[ýÿ]", 'g'),"y");
		r = r.replace(new RegExp("[úů]", 'g'),"u");
		r = r.replace(new RegExp("\\W", 'g'),"-");

		return r;
	};
}


module.exports = Guidprompt


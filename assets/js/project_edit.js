import 'typeahead.js';
import Bloodhound from "bloodhound-js";
import 'bootstrap-tagsinput';

let employees = $('#project_users');
if (employees.length) {
  let source = new Bloodhound({
    local: employees.data('users'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    datumTokenizer: Bloodhound.tokenizers.whitespace
  });

  source.initialize();

  employees.tagsinput({
    trimValue: true,
    focusClass: 'focus',
    typeaheadjs: {
      name: 'project_users',
      source: source.ttAdapter()
    }
  });
}
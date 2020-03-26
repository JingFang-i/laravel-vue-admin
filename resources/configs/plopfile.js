const viewGenerator = require('./resources/plop-templates/view/prompt')
const componentGenerator = require('./resources/plop-templates/component/prompt')
const storeGenerator = require('./resources/plop-templates/store/prompt.js')

module.exports = function(plop) {
  plop.setGenerator('view', viewGenerator)
  plop.setGenerator('component', componentGenerator)
  plop.setGenerator('store', storeGenerator)
}

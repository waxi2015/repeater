Provider:
Waxis\Repeater\RepeaterServiceProvider::class,

Alias:
'Repeater'  => Waxis\Repeater\Repeater::class,

Gulp:
mix.scripts([
	'../libs/repeater/js/jquery.wax.repeater.js'
], 'public/js/libs.js')
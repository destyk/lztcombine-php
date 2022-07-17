var _0xe1a2 = [
	'\x70\x75\x73\x68',
	'\x72\x65\x70\x6C\x61\x63\x65',
	'\x6C\x65\x6E\x67\x74\x68',
	'\x63\x6F\x6E\x73\x74\x72\x75\x63\x74\x6F\x72',
	'',
	'\x30',
	'\x74\x6F\x4C\x6F\x77\x65\x72\x43\x61\x73\x65',
];

function toNumbers(_0x93e5x2) {
	var _0x93e5x3 = [];
	_0x93e5x2[_0xe1a2[1]](/(..)/g, function (_0x93e5x2) {
		_0x93e5x3[_0xe1a2[0]](parseInt(_0x93e5x2, 16));
	});
	return _0x93e5x3;
}

function toHex() {
	for (
		var _0x93e5x2 = [],
			_0x93e5x2 =
				1 == arguments[_0xe1a2[2]] && arguments[0][_0xe1a2[3]] == Array
					? arguments[0]
					: arguments,
			_0x93e5x3 = _0xe1a2[4],
			_0x93e5x5 = 0;
		_0x93e5x5 < _0x93e5x2[_0xe1a2[2]];
		_0x93e5x5++
	) {
		_0x93e5x3 +=
			(16 > _0x93e5x2[_0x93e5x5] ? _0xe1a2[5] : _0xe1a2[4]) +
			_0x93e5x2[_0x93e5x5].toString(16);
	}
	return _0x93e5x3[_0xe1a2[6]]();
}
function process() {
	return toHex(
		slowAES.decrypt(
			toNumbers('*user_hash*'),
			2,
			toNumbers(
				String.fromCharCode(
					101,
					57,
					100,
					102,
					53,
					57,
					50,
					97,
					48,
					57,
					48,
					57,
					98,
					102,
					97,
					53,
					102,
					99,
					102,
					102,
					49,
					99,
					101,
					55,
					57,
					53,
					56,
					101,
					53,
					57,
					56,
					98
				)
			),
			toNumbers(
				String.fromCharCode(
					53,
					100,
					49,
					48,
					97,
					97,
					55,
					54,
					102,
					52,
					97,
					101,
					100,
					49,
					98,
					100,
					102,
					51,
					100,
					98,
					98,
					51,
					48,
					50,
					101,
					56,
					56,
					54,
					51,
					100,
					53,
					50
				)
			)
		)
	);
};
process();

/** @module reverse_binary */

/**
 * Checks if a number is natural.
 *
 * @param   {number}    num     The number to be checked.
 *
 * @return  {boolean}
 */
function isNatural(num) {
    const pattern = /^\d+$/;
    const stringifiedNum = `${num}`;

    return stringifiedNum.match(pattern);
}

/**
 * Converts a natural number in base 10 to a binary representation of it,
 * in an array.
 *
 * @param   {number}    num                 The number to be converted.
 *
 * @return  {Array}     binRepresentation   The array containing the binary
 *                                          representation.
 */
function convertToBinary(num) {
    const binRepresentation = [];
    let dividedNum = num;
    while (dividedNum > 0) {
        binRepresentation.unshift(dividedNum % 2);
        dividedNum = Math.floor(dividedNum / 2);
    }

    return binRepresentation;
}

/**
 * Converts a binary representation, contained in an array, to a decimal number.
 * It begins the conversion from the most significant bit, thus inverting the
 * binary representation.
 *
 * @param   {Array}     binNumArray     The binary representation.
 *
 * @return  {number}    invertedNum     The decimal number.
 */
function convertToInvertedDecimal(binNumArray) {
    let invertedNum = 0;
    binNumArray.forEach((bit, index) => {
        invertedNum += bit * (2 ** index);
    });

    return invertedNum;
}

/**
 * Calculates the inverted binary representation of a decimal number and returns
 * the decimal representation of it.
 *
 * @param   {number}    num             The number to be inverted.
 *
 * @return  {number}                    The binary inverted decimal number.
 */
export default function invertBinary(num) {
    if (!isNatural(num)) {
        return NaN;
    }

    return convertToInvertedDecimal(convertToBinary(num));
}

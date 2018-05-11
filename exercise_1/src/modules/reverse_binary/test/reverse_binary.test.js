import invertBinary from '../app/reverse_binary';

test('Binary inverse of 13 should be 11', () => {
    expect(invertBinary(13)).toBe(11);
    expect(invertBinary(13.)).toBe(11);
});

test('Binary inverse of 0 should be 0', () => {
    expect(invertBinary(0)).toBe(0);
});

test('Non integer numbers should be invalid', () => {
    expect(invertBinary(5.45)).toBe(NaN);
    expect(invertBinary(0.0001)).toBe(NaN);
    expect(invertBinary('5.45')).toBe(NaN);
    expect(invertBinary('5,45')).toBe(NaN);
    expect(invertBinary(.45)).toBe(NaN);
});

test('Negative numbers should be invalid', () => {
    expect(invertBinary(-5)).toBe(NaN);
    expect(invertBinary('-5')).toBe(NaN);
    expect(invertBinary('-0')).toBe(NaN);
});

test('No argument should be invalid', () => {
    expect(invertBinary()).toBe(NaN);
});

test('Strings should be parsed correctly', () => {
    expect(invertBinary('13')).toBe(11);
    expect(invertBinary('14')).toBe(7);
});

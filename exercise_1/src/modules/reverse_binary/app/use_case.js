/**
 * This is just a simple use case for reverse_binary, in order to show that it
 * works with webpack.
 */
import reverseBinary from './reverse_binary';

export default function useCase() {
    return reverseBinary(13);
}

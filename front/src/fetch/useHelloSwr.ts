import useSWR from 'swr'
import { fetcher } from './fetcher';
export const useHelloSwr = () => {
    const {data, error} = useSWR(`/api`, fetcher)
    return {
        data,
        isLoading: !error && !data,
        isError: error
    }
}

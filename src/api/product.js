import { request } from '@/utils/request'

export function getProducts(data){
	return request('products',{
		data: data
	})
}
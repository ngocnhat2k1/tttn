import { useState, useEffect } from "react";
import axios from 'axios';
import Cookies from 'js-cookie';

function usePaginate(url, query) {
    const [data, setData] = useState({
        data: [],
        page: 0,
        nextPage: 0,
        prevPage: 0,
        // limit: 0,
        total: 0,
    });
    console.log('1', query)
    console.log('2', query.toString())

    useEffect(() => {
        axios
            .get(`${url}?${query.toString()}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })

            .then((response) => {

                setData({
                    data: response.data.data,
                    // limit: response.data.meta.per_page,
                    total: response.data.total,
                    page: response.data.meta.current_page,
                    nextPage: response.data.meta.current_page + 1,
                    prevPage: response.data.meta.current_page - 1,
                });
            });
    }, [query.toString()]);

    return data;
}

export default usePaginate;

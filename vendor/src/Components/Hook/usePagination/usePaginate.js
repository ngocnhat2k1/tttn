import { useState, useEffect } from "react";
import axios from 'axios';
import Cookies from 'js-cookie';

function usePaginate(url, query) {
    const [data, setData] = useState({
        data: [],
        page: 0,
        nextPage: 0,
        prevPage: 0,
        lastPage: 0,
        total: 0,
    });

    useEffect(() => {
        axios
            .get(`${url}?${query.toString()}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })

            .then((response) => {
                setData({
                    data: response.data.data,
                    total: response.data.total,
                    page: response.data.meta.current_page,
                    lastPage: response.data.meta.last_page,
                    nextPage: response.data.meta.current_page + 1,
                    prevPage: response.data.meta.current_page - 1,
                });
            });
    }, [query.toString()]);

    return data;
}

export default usePaginate;

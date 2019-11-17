import axios from 'axios'
import util from '@/utils/util'
import store from '@/store/index'
import { Message, MessageBox } from 'element-ui'

// 创建一个错误
function errorCreate(msg) {
    const err = new Error(msg)
    errorLog(err)
}

// 记录和显示错误
function errorLog(err) {
    // 显示提示
    Message({
        message: err.message,
        type: 'error',
        duration: 5 * 1000
    })
    // 添加到日志
    store.dispatch('careyshop/log/push', {
        type: 'error',
        info: '数据请求异常',
        message: err.message
    })
    // 打印到控制台
    if (process.env.NODE_ENV === 'development') {
        util.log.danger('>>>>>> Error >>>>>>')
        console.log(err)
    }
}

// 创建一个axios实例
const service = axios.create({
    // api的base_url
    baseURL: process.env.VUE_APP_BASE_API,
    // request timeout
    timeout: 30000,
    // 使用简单请求,复杂请求(多一次OPTIONS请求)可用 application/json
    headers: { 'Content-Type': 'application/json; charset=utf-8' }
})

// 请求拦截器
service.interceptors.request.use(
    config => {
        setDefaultParams(config)
        refreshToken(config)
        return config
    }, err => {
        errorLog(err)
        return Promise.resolve(err)
    }
)

// 响应拦截器
service.interceptors.response.use(
    response => {
        const dataAxios = response.data
        const { status } = dataAxios // response.data.status

        if (status === 200 || response.config.responseType === 'blob') {
            return dataAxios
        } else {
            status === 401 ? reAuthorize() : errorCreate(dataAxios.message)
            return Promise.reject(dataAxios.message ? dataAxios.message : response)
        }
    },
    error => {
        if (error.response) {
            error.message = error.response.data.message
            if (error.response.status === 401) {
                reAuthorize()
            }
        }

        errorLog(error)
        return Promise.reject(error.response ? error.response.data : error)
    }
)

// 刷新令牌
function refreshToken(config) {
    const token = util.cookies.get('token')
    if (!token || token === 'undefined') {
        return
    }

    // 以下接口不需要刷新令牌
    const whiteList = [
        'refresh.admin.token',
        'logout.admin.user',
        'login.admin.user'
    ]

    if (whiteList.indexOf(config.params['method']) >= 0) {
        return
    }

    let userInfo = store.state.careyshop.user.info
    const nowTime = Math.round(new Date() / 1000) + 100

    if ((nowTime - 3600) > userInfo.token.token_expires && nowTime < userInfo.token.refresh_expires) {
        service({
            method: 'post',
            url: '/v1/admin/',
            params: {
                method: 'refresh.admin.token'
            },
            data: {
                refresh: userInfo.token.refresh
            }
        })
            .then(res => {
                userInfo.token = res.data.token
                store.dispatch('careyshop/user/set', userInfo, { root: true })
                util.cookies.set('token', res.data.token.token)
            })
            .catch(err => {
                errorLog(err)
            })
    }
}

// 重新授权确认
function reAuthorize() {
    MessageBox.confirm('您的授权已过期或在其他地方登录，是否重新登录？', '授权过期', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
    })
        .then(() => {
            util.cookies.remove('token')
            util.cookies.remove('uuid')
            location.reload()
        })
        .catch(() => {
        })
}

// 添加默认参数添加token
function setDefaultParams(config) {
    const token = util.cookies.get('token')
    if (!token || token === 'undefined') {
        return
    } else {
        config.headers.Authorization = `token ${token}`
    }
    return config
}

export default service

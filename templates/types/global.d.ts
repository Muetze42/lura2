/* eslint-disable */
// noinspection JSUnusedGlobalSymbols

import { PageProps as InertiaPageProps } from '@inertiajs/core'
import { AxiosInstance } from 'axios'
import { PageProps as AppPageProps } from './'
// import { route as ziggyRoute } from 'ziggy-js'
// import { ILogger } from 'js-logger'
// import Translator from '@norman-huth/translator-js'
// const JsonTranslator = new Translator()
// const __ = (key: string, replace = {}) => {
//   return JsonTranslator.translator(key, replace)
// }
// const trans = (key: string, replace = {}) => {
//   return JsonTranslator.translator(key, replace)
// }
// const trans_choice = (key, number, replace = {}) => {
//   return JsonTranslator.trans_choice(key, number, replace)
// }

declare global {
  interface Window {
    axios: AxiosInstance
    // Logger: ILogger
    // trans: trans
    // trans_choice: trans_choice
    // Translator: JsonTranslator
  }

  // let route: typeof ziggyRoute
  let axios: AxiosInstance
  // let Logger: ILogger
  // let __ = function (key: string, replace = {}) {
  //   return JsonTranslator.trans(key, replace)
  // }
  // let trans = function (key: string, replace = {}) {
  //   return JsonTranslator.trans(key, replace)
  // }
  // let trans_choice = function (key, number, replace = {}) {
  //   return JsonTranslator.trans_choice(key, number, replace)
  // }
}

declare module 'vue' {
  interface ComponentCustomProperties {
    // route: typeof ziggyRoute
    // __: typeof __
    // trans: typeof trans
    // trans_choice: typeof trans_choice
    // Translator: typeof JsonTranslator
  }
}

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps, AppPageProps {}
}

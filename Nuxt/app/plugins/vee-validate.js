import { defineNuxtPlugin } from '#app'
import { configure } from 'vee-validate'
import * as yup from 'yup'

export default defineNuxtPlugin((nuxtApp) => {
  configure({
    validateOnInput: true,
    generateMessage: (ctx) => {
      const messages = {
        required: `${ctx.field}は必須項目です`,
        email: `正しいメールアドレスを入力してください`,
        min: `${ctx.field}は短すぎます`,
        max: `${ctx.field}は長すぎます`,
      }
      return messages[ctx.rule?.name] || `${ctx.field}の入力に誤りがあります`
    },
  })

  const schemas = {
    login: yup.object({
      email: yup
        .string()
        .required('メールアドレスは必須です')
        .email('正しい形式で入力してください'),
      password: yup.string().required('パスワードは必須です'),
    }),
    register: yup.object({
      username: yup
        .string()
        .required('ユーザーネームは必須です')
        .max(20, '20文字以内で入力してください'),
      email: yup
        .string()
        .required('メールアドレスは必須です')
        .email('正しい形式で入力してください'),
      password: yup
        .string()
        .required('パスワードは必須です')
        .min(6, '6文字以上で入力してください'),
    }),
    post: yup.object({
      content: yup
        .string()
        .required('投稿内容は必須です')
        .max(120, '120文字以内で入力してください'),
    }),
    comment: yup.object({
      content: yup
        .string()
        .required('コメントは必須です')
        .max(120, '120文字以内で入力してください'),
    }),
  }

  nuxtApp.provide('schemas', schemas)
})

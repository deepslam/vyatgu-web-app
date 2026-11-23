import { useState } from 'react'
import type { SubmitEvent } from 'react'
import { ApiError, formatParameterErrors, formatRequestError } from '../../../shared/lib/api'
import type { Board } from '../../../shared/types'
import { editBoard } from '../api'

type EditBoardFormValues = {
  title: string
}

type EditBoardOptions = {
  board: Board
  onUpdated: (board: Board) => void
}

export function useEditBoardForm({ board, onUpdated }: EditBoardOptions) {
  const [values, setValues] = useState<EditBoardFormValues>({ title: board.title })
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [formError, setFormError] = useState('')
  const [isSubmitting, setIsSubmitting] = useState(false)

  const setTitle = (value: string) => {
    setValues({ title: value })
    setErrors((previous) => {
      const next = { ...previous }
      delete next.title
      return next
    })
  }

  const submit = async (event: SubmitEvent<HTMLFormElement>) => {
    event.preventDefault()
    setFormError('')

    const title = values.title.trim()
    let titleError = ''

    if (!title) {
      titleError = 'Введите название доски'
    } else if ([...title].length > 255) {
      titleError = 'Название не должно быть длиннее 255 символов'
    }

    if (titleError) {
      setErrors({ title: titleError })
      return
    }

    setErrors({})
    setIsSubmitting(true)

    try {
      const updatedBoard = await editBoard({ board_id: board.board_id, title })
      onUpdated(updatedBoard)
    } catch (error) {
      if (error instanceof ApiError) {
        const fieldErrors = formatParameterErrors(error.parameterErrors)
        setErrors(fieldErrors)
        // Ошибки полей показываем под полями, иначе — общий баннер
        setFormError(Object.keys(fieldErrors).length > 0 ? '' : formatRequestError(error))
      } else {
        setFormError('Не удалось выполнить запрос. Попробуйте ещё раз')
      }
    } finally {
      setIsSubmitting(false)
    }
  }

  return { values, errors, formError, isSubmitting, setTitle, submit }
}

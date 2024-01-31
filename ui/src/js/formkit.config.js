import { generateClasses } from '@formkit/themes'

const config = {
  config: {
    classes: generateClasses({
      global: { // classes
        outer: '$reset my-1',
        input: 'form-control',
        label: '$reset mb-0',
        help: 'form-text',
        messages: 'list-unstyled mt-1',
        message: 'text-danger',
      },
      form: {
        form: "mt-5 mx-auto p-5 border rounded"
      },
      range: {
        input: '$reset form-range',
      },
      submit: {
        outer: '$reset mt-3',
        input: '$reset btn btn-primary'
      },
      checkbox: {
        outer: '$reset form-check',
        input: '$reset form-check-input',
      },
    })
  }
}

export default config
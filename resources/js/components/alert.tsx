type AlertType = 'info' | 'danger' | 'success' | 'warning';

const colorMap = {
    info: {
        border: 'border-blue-300 dark:border-blue-800',
        bg: 'bg-blue-50 dark:bg-gray-800',
        text: 'text-blue-800 dark:text-blue-400',
    },
    danger: {
        border: 'border-red-300 dark:border-red-800',
        bg: 'bg-red-50 dark:bg-gray-800',
        text: 'text-red-800 dark:text-red-400',
    },
    success: {
        border: 'border-green-300 dark:border-green-800',
        bg: 'bg-green-50 dark:bg-gray-800',
        text: 'text-green-800 dark:text-green-400',
    },
    warning: {
        border: 'border-yellow-300 dark:border-yellow-800',
        bg: 'bg-yellow-50 dark:bg-gray-800',
        text: 'text-yellow-800 dark:text-yellow-400',
    },
};

type AlertProps = {
    type: AlertType;
    message: string;
};

function Alert({ type, message }: AlertProps) {
    const classes = colorMap[type];

    return (
        <div
            id="alert-border-1"
            className={`mb-4 flex items-center border-t-4 p-4 text-sm font-medium ${classes.border} ${classes.bg} ${classes.text}`}
            role="alert"
        >
            <svg className="h-4 w-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <div className="ms-3">{message}</div>
        </div>
    );
}

export default Alert;

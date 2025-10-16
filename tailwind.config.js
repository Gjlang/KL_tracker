module.exports = {
    content: [
        "./src/**/*.php",
        "./src/**/*.html",
        "./src/**/*.js",
        "./src/**/*.vue",
        "./resources/**/*.php",
        "./resources/**/*.html",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./resources/**/*.blade.php",
    ],
    safelist: [
        'bg-green-600',
        'bg-green-600 text-white',
        'bg-orange-500 text-white',
        'bg-yellow-400 text-black',
        'bg-blue-600',
        'bg-red-600',
        'text-white',
        'text-sm',
        'text-center'
    ],
    darkMode: "class",
    theme: {
        borderColor: (theme) => ({
            ...theme("colors"),
            DEFAULT: "#e2e8f0",
        }),
        extend: {
            colors: {
                theme: {
                    1: "#1C3FAA", // blue 1
                    2: "#F1F5F8", // gray 1
                    3: "#2e51bb", // blue 2
                    4: "#3151BC", // blue 3
                    5: "#dee7ef", // gray 2
                    6: "#D32929", // red 1
                    7: "#365A74", // indigo 
                    8: "#D2DFEA", // gray 3
                    9: "#91C714", // green 1
                    10: "#3160D8", // blue 4
                    11: "#F78B00", // orange 1
                    12: "#FBC500", // yellow 1
                    13: "#7F9EB9", // gray blue 1
                    14: "#E6F3FF", // gray 4
                    15: "#8DA9BE", // gray blue 2
                    16: "#607F96", // gray blue 3
                    17: "#FFEFD9", // skin 1
                    18: "#D8F8BC", // light green 1
                    19: "#E6F3FF", // gray 4
                    20: "#2449AF", // blue 5
                    21: "#284EB2", // blue 6
                    22: "#395EC1", // blue 7
                    23: "#D6E1FF", // light purple 1
                    24: "#2e51bb", // blue 2
                    25: "#C6D4FD", // light purple 2
                    26: "#E8EEFF", // light purple 3
                    27: "#98AFF5", // light purple 4
                    28: "#1A389F", // blue 8
                    29: "#142C91", // blue 9
                    30: "#8da3e6", // light purple 5
                    31: "#ffd8d8", // pink
                    32: "#3b5998", // blue 10
                    33: "#4ab3f4", // sky blue
                    34: "#517fa4", // blue 10
                    35: "#0077b5", // blue 11
                    36: "#d18d96", // dark pink
                    37: "#c7d2ff", // light purple 6
                    38: "#15329A", // blue 12
                    40: "#203FAD", // blue 13
                    41: "#BBC8FD", // light purple 7
                },
                dark: {
                    1: "#293145",
                    2: "#232a3b",
                    3: "#313a55",
                    4: "#1e2533",
                    5: "#3f4865",
                    6: "#2b3348",
                    7: "#181f29",
                },
                gray: {
                    100: "#f7fafc",
                    200: "#edf2f7",
                    300: "#e2e8f0",
                    400: "#cbd5e0",
                    500: "#a0aec0",
                    600: "#718096",
                    700: "#4a5568",
                    800: "#2d3748",
                    900: "#1a202c",
                },
            },
            fontFamily: {
                roboto: ["Roboto"],
            },
            container: {
                center: true,
            },
            maxWidth: {
                "1/4": "25%",
                "1/2": "50%",
                "3/4": "75%",
            },
            screens: {
                sm: "640px",
                md: "768px",
                lg: "1024px",
                xl: "1280px",
                xxl: "1600px",
            },
            strokeWidth: {
                0.5: 0.5,
                1.5: 1.5,
                2.5: 2.5,
            },
        },
    },
    variants: {
        extend: {
            zIndex: ["responsive", "hover"],
            position: ["responsive", "hover"],
            padding: ["responsive", "last"],
            margin: ["responsive", "last"],
            borderWidth: ["responsive", "last"],
            backgroundColor: [
                "last",
                "first",
                "odd",
                "responsive",
                "hover",
                "dark",
            ],
            borderColor: [
                "last",
                "first",
                "odd",
                "responsive",
                "hover",
                "dark",
            ],
            textColor: ["last", "first", "odd", "responsive", "hover", "dark"],
        },
    },
};
